<?php
$admin_page_title = 'Agenda';
require __DIR__ . '/includes/admin_header.php';

$stmt = db()->query('SELECT * FROM eventos ORDER BY data_evento DESC, hora_evento DESC');
$eventos = $stmt->fetchAll();

$msg = $_GET['msg'] ?? null;
?>
<div class="admin-topbar">
  <h1>Agenda de eventos</h1>
  <a href="/admin/evento-form.php" class="btn btn-teal">+ Novo evento</a>
</div>

<?php if ($msg === 'criado'): ?>
  <div class="alert alert-success">Evento criado com sucesso.</div>
<?php elseif ($msg === 'atualizado'): ?>
  <div class="alert alert-success">Evento atualizado com sucesso.</div>
<?php elseif ($msg === 'excluido'): ?>
  <div class="alert alert-success">Evento excluído.</div>
<?php endif; ?>

<?php if (empty($eventos)): ?>
  <div class="agenda-empty">Nenhum evento cadastrado ainda. Clique em "Novo evento" para começar.</div>
<?php else: ?>
  <table class="admin-table">
    <thead>
      <tr>
        <th>Título</th>
        <th>Tipo</th>
        <th>Data</th>
        <th>Local</th>
        <th>Vagas</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($eventos as $evento): $d = formatar_data_evento($evento['data_evento'], $evento['hora_evento']); ?>
        <tr>
          <td><?= e($evento['titulo']) ?></td>
          <td><?= e(ucfirst($evento['tipo'])) ?></td>
          <td><?= e($d['dia_semana']) ?>, <?= e($d['dia']) ?>/<?= date('m', strtotime($evento['data_evento'])) ?> às <?= e($d['hora']) ?>h</td>
          <td><?= e($evento['local']) ?></td>
          <td><?= $evento['vagas'] !== null ? (int) $evento['vagas'] : '—' ?></td>
          <td><span class="status-pill <?= $evento['ativo'] ? 'ativo' : 'inativo' ?>"><?= $evento['ativo'] ? 'Ativo' : 'Oculto' ?></span></td>
          <td>
            <div class="table-actions">
              <a href="/admin/evento-form.php?id=<?= (int) $evento['id'] ?>" class="btn btn-ghost btn-sm">Editar</a>
              <form method="post" action="/admin/evento-excluir.php" data-confirm="Tem certeza que deseja excluir este evento?">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= (int) $evento['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
