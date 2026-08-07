<?php
/** @var array $evento */
$d = formatar_data_evento($evento['data_evento'], $evento['hora_evento']);
$video_id = extrair_youtube_id($evento['video_youtube_url'] ?? null);
?>
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
    <?php if ($video_id): ?>
      <div class="agenda-video">
        <iframe
          src="https://www.youtube-nocookie.com/embed/<?= e($video_id) ?>"
          title="<?= e(t('agenda.video_label')) ?>"
          loading="lazy"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
        ></iframe>
      </div>
    <?php endif; ?>
    <?php if (!empty($evento['vagas'])): ?>
      <span class="agenda-vagas"><?= e(str_replace('{n}', (string) (int) $evento['vagas'], t('common.spots_available'))) ?></span>
    <?php endif; ?>

    <div class="agenda-extra-links">
      <?php if (!local_parece_online($evento['local'])): ?>
        <a href="<?= e(link_google_maps($evento['local'])) ?>" target="_blank" rel="noopener" class="agenda-chip">🗺️ <?= e(t('agenda.ver_mapa')) ?></a>
        <a href="<?= e(link_waze($evento['local'])) ?>" target="_blank" rel="noopener" class="agenda-chip">🚗 Waze</a>
      <?php endif; ?>
      <a href="<?= e(link_google_calendar($evento)) ?>" target="_blank" rel="noopener" class="agenda-chip">📅 <?= e(t('agenda.add_calendar')) ?> Google</a>
      <a href="<?= e(link_outlook_calendar($evento)) ?>" target="_blank" rel="noopener" class="agenda-chip">📅 <?= e(t('agenda.add_calendar')) ?> Outlook</a>
    </div>
  </div>
  <div class="agenda-actions">
    <?php if (!empty($evento['link_inscricao'])): ?>
      <a href="<?= e($evento['link_inscricao']) ?>" class="btn btn-accent btn-sm" target="_blank" rel="noopener"><?= e(t('common.subscribe')) ?></a>
    <?php endif; ?>
  </div>
</div>
