<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = t('loja.title');
$page_description = t('loja.description');
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="padding:90px 0 60px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker"><?= e(t('loja.hero.kicker')) ?></p>
      <h1><?= e(t('loja.hero.title')) ?></h1>
      <p class="lead" style="margin:0 auto;"><?= e(t('loja.hero.lead')) ?></p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:640px; text-align:center;">
    <div class="coming-soon">
      <span class="coming-soon__badge"><?= e(t('common.coming_soon')) ?></span>
      <h2><?= e(t('loja.soon.title')) ?></h2>
      <p><?= e(t('loja.soon.desc')) ?></p>
      <div style="display:flex; gap:14px; justify-content:center; flex-wrap:wrap;">
        <a href="<?= e(AMAZON_BOOK_URL) ?>" target="_blank" rel="noopener" class="btn btn-accent"><?= e(t('loja.soon.cta_amazon')) ?></a>
        <a href="/agende-um-horario.php" class="btn btn-ghost"><?= e(t('loja.soon.cta_agendar')) ?></a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
