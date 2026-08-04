<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/uploads.php';
exigir_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validar($_POST['csrf_token'] ?? null)) {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $foto = buscar_foto_galeria($id);
        $stmt = db()->prepare('DELETE FROM galeria_fotos WHERE id = ?');
        $stmt->execute([$id]);
        if ($foto) {
            remover_imagem_enviada($foto['arquivo']);
        }
    }
}

header('Location: /admin/galeria.php?msg=excluido');
exit;
