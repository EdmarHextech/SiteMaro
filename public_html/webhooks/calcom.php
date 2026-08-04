<?php
/**
 * Recebe o webhook "BOOKING_CREATED" do Cal.com quando um cliente agenda uma sessão
 * depois de pagar (ver agendar-sessao.php). Público e sem CSRF pelo mesmo motivo do
 * webhook do Mercado Pago — validação de assinatura substitui o CSRF aqui.
 *
 * Associa o agendamento ao pedido em duas etapas: primeiro tenta pelo `codigo` do pedido
 * (passado como metadata na hora de abrir o embed em agendar-sessao.php); se não vier por
 * algum motivo, cai para um fallback por e-mail do participante, casando com o pedido pago
 * mais recente daquele e-mail que ainda não tem agendamento.
 */
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

function calcom_webhook_responder(int $status, string $motivo): never
{
    http_response_code($status);
    echo json_encode(['status' => $motivo]);
    exit;
}

function cal_validar_assinatura_webhook(string $corpoRaw, string $assinaturaHeader): bool
{
    if (CALCOM_WEBHOOK_SECRET === '' || $assinaturaHeader === '') {
        return false;
    }
    $esperada = hash_hmac('sha256', $corpoRaw, CALCOM_WEBHOOK_SECRET);
    return hash_equals($esperada, $assinaturaHeader);
}

if (CALCOM_WEBHOOK_SECRET === '') {
    calcom_webhook_responder(200, 'calcom_nao_configurado');
}

$corpoRaw = file_get_contents('php://input');
$assinatura = $_SERVER['HTTP_X_CAL_SIGNATURE_256'] ?? '';

if (!cal_validar_assinatura_webhook($corpoRaw, $assinatura)) {
    error_log('[webhook calcom] Assinatura inválida.');
    calcom_webhook_responder(401, 'assinatura_invalida');
}

$dados = json_decode($corpoRaw, true);
if (!is_array($dados)) {
    calcom_webhook_responder(400, 'payload_invalido');
}

if (($dados['triggerEvent'] ?? '') !== 'BOOKING_CREATED') {
    calcom_webhook_responder(200, 'evento_ignorado');
}

$payload = $dados['payload'] ?? [];
$uid = $payload['uid'] ?? null;
$startTime = $payload['startTime'] ?? null;
$codigoMetadata = $payload['metadata']['codigo'] ?? null;
$emailParticipante = $payload['attendees'][0]['email'] ?? null;

if (!$uid || !$startTime) {
    calcom_webhook_responder(400, 'payload_incompleto');
}

$pedido = null;
if ($codigoMetadata) {
    $pedido = buscar_pedido_por_codigo((string) $codigoMetadata);
    // Checagem redundante: o e-mail de quem agendou deve bater com o e-mail de quem comprou.
    if ($pedido && $emailParticipante && strtolower($pedido['cliente_email']) !== strtolower($emailParticipante)) {
        error_log('[webhook calcom] E-mail do agendamento não bate com o do pedido ' . $pedido['codigo'] . ' — ignorando associação automática.');
        $pedido = null;
    }
}

if (!$pedido && $emailParticipante) {
    // Fallback: pedido pago mais recente daquele e-mail, de produto de sessão, ainda sem agendamento.
    $stmt = db()->prepare("SELECT p.* FROM pedidos p
        WHERE p.status = 'pago' AND p.agendamento_data_hora IS NULL AND LOWER(p.cliente_email) = LOWER(?)
        ORDER BY p.created_at DESC LIMIT 1");
    $stmt->execute([$emailParticipante]);
    $pedido = $stmt->fetch() ?: null;
}

if (!$pedido) {
    error_log('[webhook calcom] Não foi possível associar o agendamento ' . $uid . ' a nenhum pedido.');
    calcom_webhook_responder(200, 'pedido_nao_encontrado');
}

$dataHora = date('Y-m-d H:i:s', strtotime($startTime));
$stmt = db()->prepare('UPDATE pedidos SET agendamento_data_hora = ?, agendamento_referencia = ? WHERE id = ?');
$stmt->execute([$dataHora, $uid, $pedido['id']]);

calcom_webhook_responder(200, 'ok');
