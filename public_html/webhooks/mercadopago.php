<?php
/**
 * Recebe notificações assíncronas de pagamento do Mercado Pago.
 *
 * IMPORTANTE: este endpoint é necessariamente público e SEM verificação de CSRF —
 * é uma chamada externa do Mercado Pago, não do navegador de um usuário logado.
 * Não "conserte" isso adicionando csrf_validar() aqui; a validação de assinatura
 * abaixo (x-signature) é o que substitui o CSRF nesse contexto.
 *
 * Nunca confiar em valor/status vindos do corpo do webhook — ele é só um "vá conferir
 * agora"; a fonte da verdade é sempre a consulta autenticada a GET /v1/payments/{id}.
 */
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/mercadopago.php';

header('Content-Type: application/json; charset=utf-8');

function webhook_responder(int $status, string $motivo): never
{
    http_response_code($status);
    echo json_encode(['status' => $motivo]);
    exit;
}

if (!mp_configurado()) {
    webhook_responder(200, 'mp_nao_configurado');
}

$corpo = json_decode(file_get_contents('php://input'), true) ?: [];
$dataId = $_GET['data.id'] ?? ($corpo['data']['id'] ?? null);
$xSignature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
$xRequestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';

if (!$dataId || !$xSignature) {
    webhook_responder(400, 'payload_incompleto');
}

if (!mp_validar_assinatura_webhook($xSignature, $xRequestId, (string) $dataId)) {
    error_log('[webhook mercadopago] Assinatura inválida para data.id=' . $dataId);
    webhook_responder(401, 'assinatura_invalida');
}

try {
    $pagamento = mp_consultar_pagamento((string) $dataId);
} catch (MercadoPagoException $e) {
    error_log('[webhook mercadopago] Falha ao consultar pagamento ' . $dataId . ': ' . $e->getMessage());
    // 500 faz o Mercado Pago tentar reenviar depois — correto para uma falha transitória nossa/deles.
    webhook_responder(500, 'falha_consulta');
}

$codigo = $pagamento['external_reference'] ?? null;
$pedido = $codigo ? buscar_pedido_por_codigo($codigo) : null;
if (!$pedido) {
    // Não é erro nosso (pode ser notificação de outro ambiente/teste) — responde 200 para não gerar retry infinito.
    webhook_responder(200, 'pedido_nao_encontrado');
}

$mapaStatus = [
    'approved' => 'pago',
    'pending' => 'pendente',
    'in_process' => 'pendente',
    'rejected' => 'recusado',
    'cancelled' => 'cancelado',
    'refunded' => 'reembolsado',
];
$statusMp = $pagamento['status'] ?? 'pending';
$novoStatus = $mapaStatus[$statusMp] ?? 'pendente';

// Idempotência: se já processamos esse exact payment_id como pago, não reprocessa (MP reenvia em não-200).
if ($pedido['status'] === $novoStatus && $pedido['mp_payment_id'] === (string) $dataId) {
    webhook_responder(200, 'ja_processado');
}

$stmt = db()->prepare('UPDATE pedidos SET status=?, mp_payment_id=?, mp_status_detail=? WHERE id=?');
$stmt->execute([$novoStatus, $dataId, $pagamento['status_detail'] ?? null, $pedido['id']]);

if ($novoStatus === 'pago' && $pedido['status'] !== 'pago') {
    $pedidoAtualizado = buscar_pedido((int) $pedido['id']);
    if ($pedidoAtualizado) {
        enviar_email_pedido($pedidoAtualizado);
    }
}

webhook_responder(200, 'ok');
