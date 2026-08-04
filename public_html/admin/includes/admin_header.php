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
<script>
(function () {
  try {
    var saved = localStorage.getItem('theme');
    var theme = saved || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    if (theme === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
  } catch (e) {}
})();
</script>
</head>
<body class="admin-body">
<div class="admin-shell">
  <aside class="admin-sidebar">
    <a href="/admin/eventos.php" class="brand"><img src="/assets/img/logo-maro-white.png" alt="Maro Camargo" class="brand-logo"></a>
    <nav class="admin-nav">
      <a href="/admin/eventos.php" class="<?= $current === 'eventos.php' ? 'is-active' : '' ?>">Agenda</a>
      <a href="/admin/evento-form.php" class="<?= $current === 'evento-form.php' ? 'is-active' : '' ?>">Novo evento</a>
      <a href="/admin/posts.php" class="<?= in_array($current, ['posts.php', 'post-form.php'], true) ? 'is-active' : '' ?>">Blog</a>
      <a href="/admin/post-form.php" class="<?= $current === 'post-form.php' ? 'is-active' : '' ?>">Novo post</a>
      <a href="/admin/galeria.php" class="<?= in_array($current, ['galeria.php', 'foto-form.php'], true) ? 'is-active' : '' ?>">Galeria</a>
      <a href="/admin/foto-form.php" class="<?= $current === 'foto-form.php' ? 'is-active' : '' ?>">Nova foto</a>
      <a href="/admin/produtos.php" class="<?= in_array($current, ['produtos.php', 'produto-form.php'], true) ? 'is-active' : '' ?>">Loja</a>
      <a href="/admin/produto-form.php" class="<?= $current === 'produto-form.php' ? 'is-active' : '' ?>">Novo produto</a>
      <a href="/index.php" target="_blank">Ver site ↗</a>
      <a href="/admin/logout.php">Sair</a>
    </nav>
  </aside>
  <main class="admin-main">
    <div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
      <button type="button" class="theme-toggle" id="themeToggle" aria-label="Alternar tema claro/escuro" aria-pressed="false">
        <span class="icon-light">☀️</span><span class="icon-dark">🌙</span>
      </button>
    </div>
