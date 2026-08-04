<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/uploads.php';
exigir_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validar($_POST['csrf_token'] ?? null)) {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $produto = buscar_produto($id);
        $stmt = db()->prepare('DELETE FROM produtos WHERE id = ?');
        $stmt->execute([$id]);
        if ($produto) {
            remover_imagem_enviada($produto['imagem']);
        }
    }
}

header('Location: /admin/produtos.php?msg=excluido');
exit;
