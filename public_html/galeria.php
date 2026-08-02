<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = t('galeria.title');
$page_description = t('galeria.description');
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="padding:90px 0 60px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker"><?= e(t('galeria.hero.kicker')) ?></p>
      <h1><?= e(t('galeria.hero.title')) ?></h1>
      <p class="lead" style="margin:0 auto;"><?= e(t('galeria.hero.lead')) ?></p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:640px; text-align:center;">
    <div class="coming-soon">
      <span class="coming-soon__badge"><?= e(t('common.coming_soon')) ?></span>
      <h2><?= e(t('galeria.soon.title')) ?></h2>
      <p><?= e(t('galeria.soon.desc')) ?></p>
      <a href="<?= e(INSTAGRAM_URL) ?>" target="_blank" rel="noopener" class="btn btn-teal"><?= e(t('galeria.soon.cta')) ?></a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
