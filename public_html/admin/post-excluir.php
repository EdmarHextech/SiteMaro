<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/uploads.php';
exigir_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validar($_POST['csrf_token'] ?? null)) {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $post = buscar_post($id);
        $stmt = db()->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        if ($post) {
            remover_imagem_enviada($post['imagem_capa']);
        }
    }
}

header('Location: /admin/posts.php?msg=excluido');
exit;
