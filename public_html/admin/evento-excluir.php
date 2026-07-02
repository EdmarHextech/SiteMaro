<?php
require_once __DIR__ . '/../includes/functions.php';
exigir_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validar($_POST['csrf_token'] ?? null)) {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = db()->prepare('DELETE FROM eventos WHERE id = ?');
        $stmt->execute([$id]);
    }
}

header('Location: /admin/eventos.php?msg=excluido');
exit;
