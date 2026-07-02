<?php
/** @var string $admin_page_title */
require_once __DIR__ . '/../../includes/functions.php';
exigir_login();
$current = basename($_SERVER['SCRIPT_NAME']);
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($admin_page_title ?? 'Painel') ?> — Painel Maro Camargo</title>
<meta name="robots" content="noindex, nofollow">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin-body">
<div class="admin-shell">
  <aside class="admin-sidebar">
    <a href="/admin/eventos.php" class="brand"><img src="/assets/img/logo-maro-white.png" alt="Maro Camargo" class="brand-logo"></a>
    <nav class="admin-nav">
      <a href="/admin/eventos.php" class="<?= $current === 'eventos.php' ? 'is-active' : '' ?>">Agenda</a>
      <a href="/admin/evento-form.php" class="<?= $current === 'evento-form.php' ? 'is-active' : '' ?>">Novo evento</a>
      <a href="/index.php" target="_blank">Ver site ↗</a>
      <a href="/admin/logout.php">Sair</a>
    </nav>
  </aside>
  <main class="admin-main">
