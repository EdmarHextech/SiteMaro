<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = t('sobre.title');
$page_description = t('sobre.description');
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="padding:80px 0 70px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker"><?= e(t('sobre.hero.kicker')) ?></p>
      <h1><?= e(t('sobre.hero.title')) ?></h1>
      <p class="lead" style="margin:0 auto;"><?= e(t('sobre.hero.lead')) ?></p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container about-grid">
    <div class="about-photo">
      <img src="/assets/img/maro-camargo.jpg" alt="Maro Camargo">
      <div class="topics-tags" style="margin-top:24px;">
        <span><?= e(t('sobre.tag.dialogo')) ?></span>
        <span><?= e(t('sobre.tag.world_cafe')) ?></span>
        <span><?= e(t('sobre.tag.educacao_ambiental')) ?></span>
        <span><?= e(t('sobre.tag.cultura_organizacional')) ?></span>
        <span><?= e(t('sobre.tag.facilitacao')) ?></span>
        <span><?= e(t('sobre.tag.formacao_educadores')) ?></span>
      </div>
    </div>
    <div>
      <p class="eyebrow"><?= e(t('sobre.trajectory.eyebrow')) ?></p>
      <h2><?= e(t('sobre.trajectory.title')) ?></h2>
      <p><?= e(t('sobre.trajectory.p1')) ?></p>
      <p><?= e(t('sobre.trajectory.p2')) ?></p>
      <p><?= e(t('sobre.trajectory.p3')) ?></p>

      <ul class="credentials">
        <li>🎓 <div><strong><?= e(t('sobre.credential1_title')) ?></strong> — <?= e(t('sobre.credential1_desc')) ?></div></li>
        <li>🌎 <div><strong><?= e(t('sobre.credential2_title')) ?></strong> — <?= e(t('sobre.credential2_desc')) ?></div></li>
        <li>🌱 <div><strong><?= e(t('sobre.credential3_title')) ?></strong> — <?= e(t('sobre.credential3_desc')) ?></div></li>
        <li>🔬 <div><strong><?= e(t('sobre.credential4_title')) ?></strong> — <?= e(t('sobre.credential4_desc')) ?></div></li>
      </ul>
    </div>
  </div>
</section>

<section class="section section--tint">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow"><?= e(t('sobre.today.eyebrow')) ?></p>
      <h2><?= e(t('sobre.today.title')) ?></h2>
    </div>
    <div class="grid grid-3">
      <div class="service-card">
        <h3><?= e(t('sobre.today.card1_title')) ?></h3>
        <p><?= e(t('sobre.today.card1_desc')) ?></p>
      </div>
      <div class="service-card">
        <h3><?= e(t('sobre.today.card2_title')) ?></h3>
        <p><?= e(t('sobre.today.card2_desc')) ?></p>
      </div>
      <div class="service-card">
        <h3><?= e(t('sobre.today.card3_title')) ?></h3>
        <p><?= e(t('sobre.today.card3_desc')) ?></p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow"><?= e(t('sobre.thirdsector.eyebrow')) ?></p>
      <h2><?= e(t('sobre.thirdsector.title')) ?></h2>
      <p><?= e(t('sobre.thirdsector.lead')) ?></p>
    </div>
    <div class="topics-tags" style="justify-content:center;">
      <span><?= e(t('sobre.thirdsector.tag1')) ?></span>
      <span><?= e(t('sobre.thirdsector.tag2')) ?></span>
      <span><?= e(t('sobre.thirdsector.tag3')) ?></span>
      <span><?= e(t('sobre.thirdsector.tag4')) ?></span>
      <span><?= e(t('sobre.thirdsector.tag5')) ?></span>
      <span><?= e(t('sobre.thirdsector.tag6')) ?></span>
      <span><?= e(t('sobre.thirdsector.tag7')) ?></span>
      <span><?= e(t('sobre.thirdsector.tag8')) ?></span>
      <span><?= e(t('sobre.thirdsector.tag9')) ?></span>
    </div>
  </div>
</section>

<section class="section section--dark" style="text-align:center;">
  <div class="container" style="max-width:640px;">
    <p class="eyebrow"><?= e(t('sobre.cta.eyebrow')) ?></p>
    <h2><?= e(t('sobre.cta.title')) ?></h2>
    <p style="color:var(--teal-100);"><?= e(t('sobre.cta.lead')) ?></p>
    <div class="hero-actions" style="justify-content:center;">
      <a href="<?= e(LATTES_URL) ?>" target="_blank" rel="noopener" class="btn btn-primary"><?= e(t('footer.lattes')) ?></a>
      <a href="<?= e(LINKEDIN_URL) ?>" target="_blank" rel="noopener" class="btn btn-outline">LinkedIn</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
