<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = t('livro.title');
$page_description = t('livro.description');
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="padding:90px 0;">
  <div class="container">
    <div class="hero-copy">
      <p class="hero-kicker"><?= e(t('livro.hero.kicker')) ?></p>
      <h1><?= e(t('livro.hero.title')) ?></h1>
      <p class="lead"><?= e(t('livro.hero.lead')) ?></p>
      <div class="hero-actions">
        <a href="/contato.php" class="btn btn-primary"><?= e(t('livro.hero.cta_find')) ?></a>
        <a href="/agenda.php" class="btn btn-outline"><?= e(t('livro.hero.cta_agenda')) ?></a>
      </div>
    </div>
    <div class="hero-portrait">
      <img src="/assets/img/livro-ponto-de-encontro.jpg" alt="<?= e(t('livro.title')) ?>">
    </div>
  </div>
</section>

<section class="section">
  <div class="container book-grid">
    <div class="book-cover">
      <img src="/assets/img/livro-ponto-de-encontro.jpg" alt="<?= e(t('livro.hero.title')) ?>">
    </div>
    <div>
      <p class="eyebrow"><?= e(t('livro.about.eyebrow')) ?></p>
      <h2><?= e(t('livro.about.title')) ?></h2>
      <p><?= t('livro.about.p1') ?></p>
      <blockquote class="book-quote"><?= e(t('livro.about.quote')) ?></blockquote>
      <p><?= e(t('livro.about.p2')) ?></p>
      <p><strong><?= e(t('livro.about.author_label')) ?></strong> <?= e(t('livro.about.author_name')) ?><br>
      <strong><?= e(t('livro.about.publisher_label')) ?></strong> <?= e(t('livro.about.publisher_name')) ?></p>
    </div>
  </div>
</section>

<section class="section section--tint" style="text-align:center;">
  <div class="container" style="max-width:640px;">
    <p class="eyebrow"><?= e(t('livro.cta.eyebrow')) ?></p>
    <h2><?= e(t('livro.cta.title')) ?></h2>
    <a href="/contato.php" class="btn btn-accent"><?= e(t('livro.cta.button')) ?></a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
