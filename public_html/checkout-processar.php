<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/carrinho.php';
require_once __DIR__ . '/includes/mercadopago.php';
require_once __DIR__ . '/includes/melhorenvio.php';
iniciar_sessao();

header('Content-Type: application/json; charset=utf-8');

function checkout_erro(string $mensagem, int $status = 400): never
{
    http_response_code($status);
    echo json_encode(['erro' => $mensagem]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    checkout_erro('Método não permitido.', 405);
}

$corpo = json_decode(file_get_contents('php://input'), true);
if (!is_array($corpo)) {
    checkout_erro('Requisição inválida.');
}

if (!csrf_validar($corpo['csrf_token'] ?? null)) {
    checkout_erro('Sessão expirada. Atualize a página e tente novamente.');
}

if (!mp_configurado()) {
    checkout_erro('Checkout indisponível no momento.', 503);
}

// ---------- Recomputa o carrinho a partir do banco. NUNCA confiar em preço vindo do cliente. ----------
$itens = carrinho_conteudo();
if (empty($itens)) {
    checkout_erro('Seu carrinho está vazio.');
}

$subtotalCentavos = carrinho_subtotal_centavos();

// ---------- Valida dados do cliente e endereço ----------
$cliente = $corpo['cliente'] ?? [];
$endereco = $corpo['endereco'] ?? [];
$formData = $corpo['form_data'] ?? [];
$freteServicoEscolhido = $corpo['frete_servico_escolhido'] ?? null; // só usado para tentar casar com a opção; preço nunca vem daqui

$nome = trim((string) ($cliente['nome'] ?? ''));
$email = trim((string) ($cliente['email'] ?? ''));
$telefone = trim((string) ($cliente['telefone'] ?? ''));
$cpf = preg_replace('~\D~', '', (string) ($cliente['cpf'] ?? ''));

$cep = preg_replace('~\D~', '', (string) ($endereco['cep'] ?? ''));
$numero = trim((string) ($endereco['numero'] ?? ''));
$logradouro = trim((string) ($endereco['logradouro'] ?? ''));
$complemento = trim((string) ($endereco['complemento'] ?? ''));
$bairro = trim((string) ($endereco['bairro'] ?? ''));
$cidade = trim((string) ($endereco['cidade'] ?? ''));
$uf = strtoupper(trim((string) ($endereco['uf'] ?? '')));

if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($cpf) !== 11) {
    checkout_erro('Confira seus dados: nome, e-mail e CPF válidos são obrigatórios.');
}
if ($logradouro === '' || $numero === '' || $bairro === '' || $cidade === '' || strlen($uf) !== 2 || strlen($cep) !== 8) {
    checkout_erro('Confira o endereço de entrega — todos os campos (exceto complemento) são obrigatórios.');
}

$paymentMethodId = (string) ($formData['payment_method_id'] ?? '');
if ($paymentMethodId === '') {
    checkout_erro('Selecione uma forma de pagamento.');
}
$isPix = $paymentMethodId === 'pix';
$metodoPagamento = $isPix ? 'pix' : ((string) ($formData['payment_type_id'] ?? '') === 'debit_card' ? 'debit_card' : 'credit_card');

// ---------- Recalcula o frete no servidor, a partir do CEP validado — nunca do preço exibido no cliente ----------
$freteCentavos = FRETE_PADRAO_CENTAVOS;
$freteServico = null;
if (melhor_envio_configurado()) {
    try {
        $opcoesFrete = me_calcular_frete($cep, $itens);
        if (!empty($opcoesFrete)) {
            $escolhida = null;
            if ($freteServicoEscolhido !== null) {
                foreach ($opcoesFrete as $opcao) {
                    if ($opcao['servico'] === $freteServicoEscolhido) {
                        $escolhida = $opcao;
                        break;
                    }
                }
            }
            $escolhida ??= $opcoesFrete[0]; // mais barata, se a escolhida não existir mais na recotação
            $freteCentavos = $escolhida['preco_centavos'];
            $freteServico = $escolhida['servico'];
        }
    } catch (MelhorEnvioException $e) {
        error_log('[checkout] Falha ao recalcular frete para pedido: ' . $e->getMessage());
        // segue com FRETE_PADRAO_CENTAVOS — não bloqueia a venda por uma instabilidade do Melhor Envio
    }
}
$totalCentavos = $subtotalCentavos + $freteCentavos;

// ---------- Cria o pedido como "pendente" antes de chamar o Mercado Pago (garante rastro mesmo se a API falhar) ----------
$codigo = gerar_codigo_pedido();
db()->beginTransaction();
try {
    $stmt = db()->prepare('INSERT INTO pedidos
        (codigo, cliente_nome, cliente_email, cliente_telefone, cliente_cpf,
         endereco_cep, endereco_logradouro, endereco_numero, endereco_complemento, endereco_bairro, endereco_cidade, endereco_uf,
         subtotal_centavos, frete_centavos, frete_servico, total_centavos, metodo_pagamento, status, ip_criacao)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([
        $codigo, $nome, $email, $telefone ?: null, $cpf,
        $cep, $logradouro, $numero, $complemento ?: null, $bairro, $cidade, $uf,
        $subtotalCentavos, $freteCentavos, $freteServico, $totalCentavos, $metodoPagamento, 'pendente', $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
    $pedidoId = (int) db()->lastInsertId();

    $stmtItem = db()->prepare('INSERT INTO pedido_itens (pedido_id, produto_id, produto_nome_snapshot, preco_unitario_centavos, quantidade, dedicatoria_texto) VALUES (?,?,?,?,?,?)');
    foreach ($itens as $item) {
        $stmtItem->execute([
            $pedidoId,
            $item['produto']['id'],
            $item['produto']['nome'],
            $item['produto']['preco_centavos'],
            $item['quantidade'],
            $item['dedicatoria_texto'],
        ]);
    }
    db()->commit();
} catch (Throwable $e) {
    db()->rollBack();
    checkout_erro('Não foi possível registrar o pedido. Tente novamente.', 500);
}

// ---------- Monta e envia o pagamento para o Mercado Pago ----------
$payload = [
    'transaction_amount' => round($totalCentavos / 100, 2), // valor SEMPRE recalculado no servidor
    'description' => 'Pedido ' . $codigo . ' — ' . SITE_NAME,
    'external_reference' => $codigo,
    'payment_method_id' => $paymentMethodId,
    'payer' => [
        'email' => $email,
        'first_name' => $nome,
        'identification' => [
            'type' => 'CPF',
            'number' => $cpf,
        ],
    ],
    'notification_url' => rtrim(SITE_URL, '/') . '/webhooks/mercadopago.php',
];
if (!empty($formData['token'])) {
    $payload['token'] = $formData['token'];
}
if (isset($formData['installments'])) {
    $payload['installments'] = (int) $formData['installments'];
}
if (!empty($formData['issuer_id'])) {
    $payload['issuer_id'] = $formData['issuer_id'];
}

try {
    $resposta = mp_criar_pagamento($payload, $codigo);
} catch (MercadoPagoException $e) {
    // Pedido fica registrado como "pendente" para ela conseguir acompanhar/contatar o cliente manualmente.
    error_log('[checkout] Falha ao criar pagamento MP para pedido ' . $codigo . ': ' . $e->getMessage());
    checkout_erro('Não foi possível processar o pagamento. Verifique os dados do cartão ou tente novamente.', 502);
}

$statusMp = $resposta['status'] ?? 'pending';
$mapaStatus = [
    'approved' => 'pago',
    'pending' => 'pendente',
    'in_process' => 'pendente',
    'rejected' => 'recusado',
    'cancelled' => 'cancelado',
    'refunded' => 'reembolsado',
];
$statusPedido = $mapaStatus[$statusMp] ?? 'pendente';

$stmtUpdate = db()->prepare('UPDATE pedidos SET status=?, mp_payment_id=?, mp_status_detail=? WHERE id=?');
$stmtUpdate->execute([$statusPedido, $resposta['id'] ?? null, $resposta['status_detail'] ?? null, $pedidoId]);

carrinho_esvaziar();

if ($statusPedido === 'pago') {
    $pedidoAtualizado = buscar_pedido($pedidoId);
    if ($pedidoAtualizado) {
        enviar_email_pedido($pedidoAtualizado);
    }
}

echo json_encode(['redirect' => '/pedido-confirmado.php?codigo=' . rawurlencode($codigo)]);
