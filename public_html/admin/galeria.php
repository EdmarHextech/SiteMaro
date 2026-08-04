<?php
$admin_page_title = 'Galeria';
require __DIR__ . '/includes/admin_header.php';

$fotos = buscar_fotos_galeria_admin();
$msg = $_GET['msg'] ?? null;
?>
<div class="admin-topbar">
  <h1>Galeria de fotos</h1>
  <a href="/admin/foto-form.php" class="btn btn-teal">+ Nova foto</a>
</div>

<?php if ($msg === 'criado'): ?>
  <div class="alert alert-success">Foto adicionada com sucesso.</div>
<?php elseif ($msg === 'atualizado'): ?>
  <div class="alert alert-success">Foto atualizada com sucesso.</div>
<?php elseif ($msg === 'excluido'): ?>
  <div class="alert alert-success">Foto excluída.</div>
<?php endif; ?>

<?php if (empty($fotos)): ?>
  <div class="agenda-empty">Nenhuma foto cadastrada ainda. Clique em "Nova foto" para começar.</div>
<?php else: ?>
  <div class="admin-photo-grid">
    <?php foreach ($fotos as $foto): ?>
      <div class="admin-photo-card">
        <img src="<?= e($foto['arquivo']) ?>" alt="<?= e($foto['legenda'] ?? '') ?>">
        <div class="admin-photo-card__body">
          <p class="admin-photo-card__evento"><?= $foto['evento_titulo'] ? e($foto['evento_titulo']) : 'Sem evento' ?></p>
          <?php if (!empty($foto['legenda'])): ?><p class="admin-photo-card__legenda"><?= e($foto['legenda']) ?></p><?php endif; ?>
          <span class="status-pill <?= $foto['ativo'] ? 'ativo' : 'inativo' ?>"><?= $foto['ativo'] ? 'Visível' : 'Oculta' ?></span>
          <div class="table-actions" style="margin-top:10px;">
            <a href="/admin/foto-form.php?id=<?= (int) $foto['id'] ?>" class="btn btn-ghost btn-sm">Editar</a>
            <form method="post" action="/admin/foto-excluir.php" data-confirm="Tem certeza que deseja excluir esta foto?">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int) $foto['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
