<?php
// Endpoint chamado via fetch() pelo plugin de imagem do TinyMCE (post-form.php) para inserir
// imagens dentro do corpo do post. Autenticado + CSRF, sempre responde em JSON.
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/uploads.php';

header('Content-Type: application/json; charset=utf-8');

if (!admin_logado()) {
    http_response_code(403);
    echo json_encode(['error' => ['message' => 'Não autenticado.']]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_validar($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    echo json_encode(['error' => ['message' => 'Requisição inválida.']]);
    exit;
}

$erro = null;
$caminho = salvar_imagem_enviada($_FILES['file'] ?? [], 'blog', $erro);

if ($caminho === null) {
    http_response_code(400);
    echo json_encode(['error' => ['message' => $erro ?? 'Falha no upload.']]);
    exit;
}

echo json_encode(['location' => $caminho]);
