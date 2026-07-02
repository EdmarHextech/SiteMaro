<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Agenda de Palestras e Eventos — Maro Camargo';
$page_description = 'Confira as próximas palestras, consultorias e eventos com Maro Camargo: data, horário, local e link de inscrição.';
require __DIR__ . '/includes/header.php';

$eventos = buscar_eventos();
?>

<section class="hero" style="padding:90px 0 70px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker">Agenda</p>
      <h1>Próximos encontros</h1>
      <p class="lead" style="margin:0 auto;">Data, horário, local e inscrição para as próximas palestras e consultorias com a Maro.</p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (empty($eventos)): ?>
      <div class="agenda-empty">
        <h3>Nenhum evento agendado no momento</h3>
        <p>Siga o <a href="<?= e(INSTAGRAM_URL) ?>" target="_blank" rel="noopener">Instagram</a> da Maro para ser avisado sobre as próximas datas.</p>
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
              <span class="agenda-badge"><?= e(ucfirst($evento['tipo'])) ?></span>
              <h3><?= e($evento['titulo']) ?></h3>
              <div class="agenda-meta">
                <span><strong><?= e($d['dia_semana']) ?></strong> · <?= e($d['hora']) ?>h</span>
                <span>📍 <?= e($evento['local']) ?></span>
              </div>
              <?php if (!empty($evento['descricao'])): ?>
                <p style="margin:8px 0 0; color:var(--ink-soft);"><?= e($evento['descricao']) ?></p>
              <?php endif; ?>
              <?php if (!empty($evento['vagas'])): ?>
                <span class="agenda-vagas"><?= (int) $evento['vagas'] ?> vagas disponíveis</span>
              <?php endif; ?>
            </div>
            <div class="agenda-actions">
              <?php if (!empty($evento['link_inscricao'])): ?>
                <a href="<?= e($evento['link_inscricao']) ?>" class="btn btn-accent btn-sm" target="_blank" rel="noopener">Inscrever-se</a>
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
    <p class="eyebrow">Quer levar a Maro para o seu evento?</p>
    <h2>Solicite uma proposta de palestra ou consultoria</h2>
    <a href="/contato.php" class="btn btn-teal">Falar com a Maro</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
