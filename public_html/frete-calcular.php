<?php
// Endpoint público (sem login) chamado via fetch() do carrinho/checkout para calcular frete
// em tempo real. Sem CSRF (não muda estado, só consulta), mas com validação de CEP e um
// limite simples de chamadas por sessão para não virar um proxy gratuito da API do Melhor Envio.
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/carrinho.php';
require_once __DIR__ . '/includes/melhorenvio.php';
iniciar_sessao();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$_SESSION['frete_chamadas'] = ($_SESSION['frete_chamadas'] ?? 0) + 1;
if ($_SESSION['frete_chamadas'] > 60) {
    http_response_code(429);
    echo json_encode(['erro' => 'Muitas tentativas. Tente novamente em instantes.']);
    exit;
}

$corpo = json_decode(file_get_contents('php://input'), true) ?: [];
$cep = preg_replace('~\D~', '', (string) ($corpo['cep'] ?? ''));

if (strlen($cep) !== 8) {
    http_response_code(400);
    echo json_encode(['erro' => 'CEP inválido.']);
    exit;
}

$itens = carrinho_conteudo();
if (empty($itens)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Carrinho vazio.']);
    exit;
}

if (!melhor_envio_configurado()) {
    echo json_encode([
        'opcoes' => [],
        'fallback' => true,
        'frete_padrao_centavos' => FRETE_PADRAO_CENTAVOS,
    ]);
    exit;
}

try {
    $opcoes = me_calcular_frete($cep, $itens);
} catch (MelhorEnvioException $e) {
    error_log('[frete-calcular] ' . $e->getMessage());
    echo json_encode([
        'opcoes' => [],
        'fallback' => true,
        'frete_padrao_centavos' => FRETE_PADRAO_CENTAVOS,
    ]);
    exit;
}

if (empty($opcoes)) {
    echo json_encode(['opcoes' => [], 'fallback' => true, 'frete_padrao_centavos' => FRETE_PADRAO_CENTAVOS]);
    exit;
}

echo json_encode(['opcoes' => $opcoes, 'fallback' => false]);
