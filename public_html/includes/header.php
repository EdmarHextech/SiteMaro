<?php
/** @var string $page_title */
/** @var string $page_description */
require_once __DIR__ . '/functions.php';
current_lang(); // resolve e persiste o idioma (via cookie) antes de qualquer saída HTML
$current = basename($_SERVER['SCRIPT_NAME']);
?><!DOCTYPE html>
<html lang="<?= e(html_lang_attr()) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title ?? SITE_NAME) ?></title>
<meta name="description" content="<?= e($page_description ?? t('meta.default_description')) ?>">
<link rel="canonical" href="<?= e(SITE_URL) ?>">
<meta property="og:title" content="<?= e($page_title ?? SITE_NAME) ?>">
<meta property="og:description" content="<?= e($page_description ?? t('meta.default_description')) ?>">
<meta property="og:image" content="<?= e(SITE_URL) ?>/assets/img/maro-camargo.jpg">
<meta property="og:type" content="website">
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="icon" href="/assets/img/favicon-96x96.png" sizes="96x96" type="image/png">
<link rel="shortcut icon" href="/assets/img/favicon.ico">
<link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
<link rel="manifest" href="/assets/img/site.webmanifest">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
<body>
<header class="site-header">
  <div class="container site-header__inner">
    <a href="/index.php" class="brand"><img src="/assets/img/logo-maro.png" alt="Maro Camargo" class="brand-logo"></a>
    <button class="nav-toggle" id="navToggle" aria-label="<?= e(t('nav.open_menu')) ?>" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
    <nav class="site-nav" id="siteNav">
      <a href="/index.php" class="<?= $current === 'index.php' ? 'is-active' : '' ?>"><?= e(t('nav.home')) ?></a>
      <a href="/sobre.php" class="<?= $current === 'sobre.php' ? 'is-active' : '' ?>"><?= e(t('nav.about')) ?></a>
      <a href="/livro.php" class="<?= $current === 'livro.php' ? 'is-active' : '' ?>"><?= e(t('nav.book')) ?></a>
      <a href="/palestras-consultorias.php" class="<?= $current === 'palestras-consultorias.php' ? 'is-active' : '' ?>"><?= e(t('nav.services')) ?></a>
      <a href="/agenda.php" class="<?= $current === 'agenda.php' ? 'is-active' : '' ?>"><?= e(t('nav.agenda')) ?></a>
      <a href="/contato.php" class="<?= $current === 'contato.php' ? 'is-active' : '' ?> nav-cta"><?= e(t('nav.contact')) ?></a>
      <div class="header-utils">
        <div class="lang-switch">
          <a href="<?= e(link_idioma('pt')) ?>" class="site-nav-lang <?= current_lang() === 'pt' ? 'is-active' : '' ?>">PT</a>
          <a href="<?= e(link_idioma('en')) ?>" class="site-nav-lang <?= current_lang() === 'en' ? 'is-active' : '' ?>">EN</a>
          <a href="<?= e(link_idioma('es')) ?>" class="site-nav-lang <?= current_lang() === 'es' ? 'is-active' : '' ?>">ES</a>
        </div>
        <button type="button" class="theme-toggle" id="themeToggle" aria-label="<?= e(t('theme.toggle_label')) ?>" aria-pressed="false">
          <span class="icon-light">☀️</span><span class="icon-dark">🌙</span>
        </button>
      </div>
    </nav>
  </div>
</header>
<main>
