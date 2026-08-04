<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = t('galeria.title');
$page_description = t('galeria.description');
require __DIR__ . '/includes/header.php';

$fotos = buscar_fotos_galeria();
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
  <div class="container">
    <?php if (empty($fotos)): ?>
      <div class="coming-soon" style="max-width:640px; margin:0 auto; text-align:center;">
        <span class="coming-soon__badge"><?= e(t('common.coming_soon')) ?></span>
        <h2><?= e(t('galeria.soon.title')) ?></h2>
        <p><?= e(t('galeria.soon.desc')) ?></p>
        <a href="<?= e(INSTAGRAM_URL) ?>" target="_blank" rel="noopener" class="btn btn-teal"><?= e(t('galeria.soon.cta')) ?></a>
      </div>
    <?php else: ?>
      <div class="gallery-grid">
        <?php foreach ($fotos as $i => $foto): ?>
          <button
            type="button"
            class="gallery-item"
            data-lightbox-trigger
            data-src="<?= e($foto['arquivo']) ?>"
            data-legenda="<?= e(trim(($foto['legenda'] ?? '') . ($foto['evento_titulo'] ? ' — ' . $foto['evento_titulo'] : ''))) ?>"
          >
            <img src="<?= e($foto['arquivo']) ?>" alt="<?= e($foto['legenda'] ?? $foto['evento_titulo'] ?? '') ?>" loading="lazy">
          </button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<div class="lightbox-overlay" id="lightboxOverlay" hidden>
  <button type="button" class="lightbox-close" id="lightboxClose" aria-label="Fechar">✕</button>
  <figure>
    <img id="lightboxImg" src="" alt="">
    <figcaption id="lightboxCaption"></figcaption>
  </figure>
</div>

<script src="/assets/js/galeria.js"></script>

<?php require __DIR__ . '/includes/footer.php'; ?>
