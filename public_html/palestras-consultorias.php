<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = t('pc.title');
$page_description = t('pc.description');
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="padding:90px 0 70px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker"><?= e(t('pc.hero.kicker')) ?></p>
      <h1><?= e(t('pc.hero.title')) ?></h1>
      <p class="lead" style="margin:0 auto;"><?= e(t('pc.hero.lead')) ?></p>
      <div class="hero-actions" style="justify-content:center;">
        <a href="/contato.php" class="btn btn-primary"><?= e(t('pc.hero.cta')) ?></a>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-3">
      <div class="service-card">
        <h3><?= e(t('pc.card1.title')) ?></h3>
        <p><?= e(t('pc.card1.desc')) ?></p>
        <ul>
          <li><?= e(t('pc.card1.li1')) ?></li>
          <li><?= e(t('pc.card1.li2')) ?></li>
          <li><?= e(t('pc.card1.li3')) ?></li>
        </ul>
      </div>
      <div class="service-card">
        <h3><?= e(t('pc.card2.title')) ?></h3>
        <p><?= e(t('pc.card2.desc')) ?></p>
        <ul>
          <li><?= e(t('pc.card2.li1')) ?></li>
          <li><?= e(t('pc.card2.li2')) ?></li>
          <li><?= e(t('pc.card2.li3')) ?></li>
        </ul>
      </div>
      <div class="service-card">
        <h3><?= e(t('pc.card3.title')) ?></h3>
        <p><?= e(t('pc.card3.desc')) ?></p>
        <ul>
          <li><?= e(t('pc.card3.li1')) ?></li>
          <li><?= e(t('pc.card3.li2')) ?></li>
          <li><?= e(t('pc.card3.li3')) ?></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="section section--dark">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow"><?= e(t('pc.audience.eyebrow')) ?></p>
      <h2><?= e(t('pc.audience.title')) ?></h2>
      <p style="color:var(--teal-100);"><?= e(t('pc.audience.lead')) ?></p>
    </div>
    <div class="topics-tags" style="justify-content:center;">
      <span><?= e(t('pc.audience.tag1')) ?></span>
      <span><?= e(t('pc.audience.tag2')) ?></span>
      <span><?= e(t('pc.audience.tag3')) ?></span>
      <span><?= e(t('pc.audience.tag4')) ?></span>
      <span><?= e(t('pc.audience.tag5')) ?></span>
      <span><?= e(t('pc.audience.tag6')) ?></span>
    </div>
  </div>
</section>

<section class="section" style="text-align:center;">
  <div class="container" style="max-width:640px;">
    <p class="eyebrow"><?= e(t('pc.cta.eyebrow')) ?></p>
    <h2><?= e(t('pc.cta.title')) ?></h2>
    <a href="/contato.php" class="btn btn-accent"><?= e(t('common.talk_to_maro')) ?></a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
