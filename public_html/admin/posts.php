<?php
$admin_page_title = 'Blog';
require __DIR__ . '/includes/admin_header.php';

$stmt = db()->query('SELECT * FROM posts ORDER BY created_at DESC');
$posts = $stmt->fetchAll();

$msg = $_GET['msg'] ?? null;
?>
<div class="admin-topbar">
  <h1>Posts do blog</h1>
  <a href="/admin/post-form.php" class="btn btn-teal">+ Novo post</a>
</div>

<?php if ($msg === 'criado'): ?>
  <div class="alert alert-success">Post criado com sucesso.</div>
<?php elseif ($msg === 'atualizado'): ?>
  <div class="alert alert-success">Post atualizado com sucesso.</div>
<?php elseif ($msg === 'excluido'): ?>
  <div class="alert alert-success">Post excluído.</div>
<?php endif; ?>

<?php if (empty($posts)): ?>
  <div class="agenda-empty">Nenhum post criado ainda. Clique em "Novo post" para começar.</div>
<?php else: ?>
  <table class="admin-table">
    <thead>
      <tr>
        <th>Título</th>
        <th>Status</th>
        <th>Publicado em</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($posts as $post): ?>
        <tr>
          <td><?= e($post['titulo']) ?></td>
          <td><span class="status-pill <?= $post['status'] === 'publicado' ? 'ativo' : 'inativo' ?>"><?= $post['status'] === 'publicado' ? 'Publicado' : 'Rascunho' ?></span></td>
          <td><?= $post['publicado_em'] ? date('d/m/Y H:i', strtotime($post['publicado_em'])) : '—' ?></td>
          <td>
            <div class="table-actions">
              <a href="/admin/post-form.php?id=<?= (int) $post['id'] ?>" class="btn btn-ghost btn-sm">Editar</a>
              <form method="post" action="/admin/post-excluir.php" data-confirm="Tem certeza que deseja excluir este post?">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
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
