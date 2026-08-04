<?php
$admin_page_title = 'Loja';
require __DIR__ . '/includes/admin_header.php';

$produtos = db()->query('SELECT * FROM produtos ORDER BY tipo ASC, nome ASC')->fetchAll();
$msg = $_GET['msg'] ?? null;
$rotuloTipo = ['fisico' => 'Físico', 'sessao' => 'Sessão'];
?>
<div class="admin-topbar">
  <h1>Produtos da loja</h1>
  <a href="/admin/produto-form.php" class="btn btn-teal">+ Novo produto</a>
</div>

<?php if ($msg === 'criado'): ?>
  <div class="alert alert-success">Produto criado com sucesso.</div>
<?php elseif ($msg === 'atualizado'): ?>
  <div class="alert alert-success">Produto atualizado com sucesso.</div>
<?php elseif ($msg === 'excluido'): ?>
  <div class="alert alert-success">Produto excluído.</div>
<?php endif; ?>

<?php if (empty($produtos)): ?>
  <div class="agenda-empty">Nenhum produto cadastrado ainda. Clique em "Novo produto" para começar.</div>
<?php else: ?>
  <table class="admin-table">
    <thead>
      <tr>
        <th>Nome</th>
        <th>Tipo</th>
        <th>Preço</th>
        <th>Estoque</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($produtos as $produto): ?>
        <tr>
          <td><?= e($produto['nome']) ?></td>
          <td><?= e($rotuloTipo[$produto['tipo']]) ?></td>
          <td><?= e(formatar_preco((int) $produto['preco_centavos'])) ?></td>
          <td><?= $produto['estoque'] !== null ? (int) $produto['estoque'] : '—' ?></td>
          <td><span class="status-pill <?= $produto['ativo'] ? 'ativo' : 'inativo' ?>"><?= $produto['ativo'] ? 'Ativo' : 'Oculto' ?></span></td>
          <td>
            <div class="table-actions">
              <a href="/admin/produto-form.php?id=<?= (int) $produto['id'] ?>" class="btn btn-ghost btn-sm">Editar</a>
              <form method="post" action="/admin/produto-excluir.php" data-confirm="Tem certeza que deseja excluir este produto?">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= (int) $produto['id'] ?>">
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
