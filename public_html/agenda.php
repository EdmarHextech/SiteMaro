<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = t('agenda.title');
$page_description = t('agenda.description');
require __DIR__ . '/includes/header.php';

$eventos = buscar_eventos();
?>

<section class="hero" style="padding:90px 0 70px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker"><?= e(t('agenda.hero.kicker')) ?></p>
      <h1><?= e(t('agenda.hero.title')) ?></h1>
      <p class="lead" style="margin:0 auto;"><?= e(t('agenda.hero.lead')) ?></p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (empty($eventos)): ?>
      <div class="agenda-empty">
        <h3><?= e(t('agenda.empty.title')) ?></h3>
        <p><?= e(t('agenda.empty.desc_prefix')) ?> <a href="<?= e(INSTAGRAM_URL) ?>" target="_blank" rel="noopener"><?= e(t('agenda.empty.desc_link')) ?></a> <?= e(t('agenda.empty.desc_suffix')) ?></p>
      </div>
    <?php else: ?>
      <div class="agenda-list">
        <?php foreach ($eventos as $evento): ?>
          <?php require __DIR__ . '/includes/agenda-card.php'; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="section section--tint" style="text-align:center;">
  <div class="container" style="max-width:640px;">
    <p class="eyebrow"><?= e(t('agenda.cta.eyebrow')) ?></p>
    <h2><?= e(t('agenda.cta.title')) ?></h2>
    <a href="/contato.php" class="btn btn-teal"><?= e(t('common.talk_to_maro')) ?></a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
