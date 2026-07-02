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
        <?php foreach ($eventos as $evento): $d = formatar_data_evento($evento['data_evento'], $evento['hora_evento']); ?>
          <div class="agenda-card">
            <div class="agenda-date">
              <span class="dia"><?= e($d['dia']) ?></span>
              <span class="mes"><?= e($d['mes']) ?></span>
            </div>
            <div class="agenda-info">
              <span class="agenda-badge"><?= e(t('common.type_' . $evento['tipo'])) ?></span>
              <h3><?= e($evento['titulo']) ?></h3>
              <div class="agenda-meta">
                <span><strong><?= e($d['dia_semana']) ?></strong> · <?= e($d['hora']) ?>h</span>
                <span>📍 <?= e($evento['local']) ?></span>
              </div>
              <?php if (!empty($evento['descricao'])): ?>
                <p style="margin:8px 0 0; color:var(--text-soft);"><?= e($evento['descricao']) ?></p>
              <?php endif; ?>
              <?php if (!empty($evento['vagas'])): ?>
                <span class="agenda-vagas"><?= e(str_replace('{n}', (string) (int) $evento['vagas'], t('common.spots_available'))) ?></span>
              <?php endif; ?>
            </div>
            <div class="agenda-actions">
              <?php if (!empty($evento['link_inscricao'])): ?>
                <a href="<?= e($evento['link_inscricao']) ?>" class="btn btn-accent btn-sm" target="_blank" rel="noopener"><?= e(t('common.subscribe')) ?></a>
              <?php endif; ?>
            </div>
          </div>
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
