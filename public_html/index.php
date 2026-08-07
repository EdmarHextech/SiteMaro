<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = t('home.title');
$page_description = t('home.description');
require __DIR__ . '/includes/header.php';

$proximos_eventos = array_slice(buscar_eventos(), 0, 2);
?>

<section class="hero">
  <div class="container">
    <div class="hero-copy">
      <p class="hero-kicker"><?= e(t('home.hero.kicker')) ?></p>
      <h1><?= t('home.hero.title') ?></h1>
      <p class="lead"><?= e(t('home.hero.lead')) ?></p>
      <div class="hero-actions">
        <a href="/agenda.php" class="btn btn-primary"><?= e(t('home.hero.cta_agenda')) ?></a>
        <a href="/livro.php" class="btn btn-outline"><?= e(t('home.hero.cta_book')) ?></a>
      </div>
    </div>
    <div class="hero-portrait">
      <img src="/assets/img/maro-camargo.jpg" alt="<?= e(t('home.hero.portrait_alt')) ?>">
    </div>
  </div>
</section>

<section class="section">
  <div class="container about-grid">
    <div class="about-photo">
      <img src="/assets/img/livro-ponto-de-encontro.jpg" alt="<?= e(t('livro.title')) ?>">
    </div>
    <div>
      <p class="eyebrow"><?= e(t('home.about.eyebrow')) ?></p>
      <h2><?= e(t('home.about.title')) ?></h2>
      <p><?= e(t('home.about.p1')) ?></p>
      <p><?= t('home.about.p2') ?></p>
      <a href="/sobre.php" class="btn btn-teal"><?= e(t('home.about.cta')) ?></a>
    </div>
  </div>
</section>

<section class="section section--tint">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow"><?= e(t('home.services.eyebrow')) ?></p>
      <h2><?= e(t('home.services.title')) ?></h2>
      <p><?= e(t('home.services.lead')) ?></p>
    </div>
    <div class="grid grid-3">
      <div class="service-card">
        <h3><?= e(t('home.services.card1_title')) ?></h3>
        <p><?= e(t('home.services.card1_desc')) ?></p>
      </div>
      <div class="service-card">
        <h3><?= e(t('home.services.card2_title')) ?></h3>
        <p><?= e(t('home.services.card2_desc')) ?></p>
      </div>
      <div class="service-card">
        <h3><?= e(t('home.services.card3_title')) ?></h3>
        <p><?= e(t('home.services.card3_desc')) ?></p>
      </div>
    </div>
    <p style="text-align:center; margin-top:36px;">
      <a href="/palestras-consultorias.php" class="btn btn-teal"><?= e(t('home.services.cta')) ?></a>
    </p>
  </div>
</section>

<section class="section section--dark">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow"><?= e(t('home.agenda.eyebrow')) ?></p>
      <h2><?= e(t('home.agenda.title')) ?></h2>
      <p><?= e(t('home.agenda.lead')) ?></p>
    </div>

    <?php if (empty($proximos_eventos)): ?>
      <div class="agenda-empty" style="background:rgba(255,255,255,0.06); color:var(--teal-100);">
        <?= e(t('home.agenda.empty')) ?>
      </div>
    <?php else: ?>
      <div class="agenda-list">
        <?php foreach ($proximos_eventos as $evento): ?>
          <?php require __DIR__ . '/includes/agenda-card.php'; ?>
        <?php endforeach; ?>
      </div>
      <p style="text-align:center; margin-top:36px;">
        <a href="/agenda.php" class="btn btn-outline"><?= e(t('home.agenda.cta')) ?></a>
      </p>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container" style="text-align:center; max-width:680px;">
    <p class="eyebrow"><?= e(t('home.cta.eyebrow')) ?></p>
    <h2><?= e(t('home.cta.title')) ?></h2>
    <p style="color:var(--text-soft); font-size:1.05rem;"><?= e(t('home.cta.lead')) ?></p>
    <a href="/contato.php" class="btn btn-accent"><?= e(t('common.talk_to_maro')) ?></a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
