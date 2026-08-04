<?php
$admin_page_title = 'Pedidos';
require __DIR__ . '/includes/admin_header.php';

$filtroStatus = $_GET['status'] ?? '';
$statusValidos = ['pendente', 'pago', 'recusado', 'cancelado', 'reembolsado'];

$sql = 'SELECT * FROM pedidos WHERE 1=1';
$params = [];
if (in_array($filtroStatus, $statusValidos, true)) {
    $sql .= ' AND status = ?';
    $params[] = $filtroStatus;
}
$sql .= ' ORDER BY created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$pedidos = $stmt->fetchAll();

$rotuloStatus = ['pendente' => 'Pendente', 'pago' => 'Pago', 'recusado' => 'Recusado', 'cancelado' => 'Cancelado', 'reembolsado' => 'Reembolsado'];
?>
<div class="admin-topbar">
  <h1>Pedidos da loja</h1>
</div>

<div style="display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap;">
  <a href="/admin/pedidos.php" class="btn btn-sm <?= $filtroStatus === '' ? 'btn-teal' : 'btn-ghost' ?>">Todos</a>
  <?php foreach ($rotuloStatus as $valor => $label): ?>
    <a href="/admin/pedidos.php?status=<?= e($valor) ?>" class="btn btn-sm <?= $filtroStatus === $valor ? 'btn-teal' : 'btn-ghost' ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
</div>

<?php if (empty($pedidos)): ?>
  <div class="agenda-empty">Nenhum pedido encontrado.</div>
<?php else: ?>
  <table class="admin-table">
    <thead>
      <tr>
        <th>Código</th>
        <th>Cliente</th>
        <th>Total</th>
        <th>Pagamento</th>
        <th>Status</th>
        <th>Data</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($pedidos as $pedido): ?>
        <tr>
          <td><?= e($pedido['codigo']) ?></td>
          <td><?= e($pedido['cliente_nome']) ?></td>
          <td><?= e(formatar_preco((int) $pedido['total_centavos'])) ?></td>
          <td><?= e($pedido['metodo_pagamento'] ?? '—') ?></td>
          <td><span class="status-pill <?= $pedido['status'] === 'pago' ? 'ativo' : 'inativo' ?>"><?= e($rotuloStatus[$pedido['status']] ?? $pedido['status']) ?></span></td>
          <td><?= e(date('d/m/Y H:i', strtotime($pedido['created_at']))) ?></td>
          <td class="table-actions">
            <a href="/admin/pedido-detalhe.php?id=<?= (int) $pedido['id'] ?>" class="btn btn-ghost btn-sm">Ver</a>
            <?php if (!empty($pedido['endereco_cep'])): ?>
              <a href="/admin/pedido-etiqueta.php?id=<?= (int) $pedido['id'] ?>" class="btn btn-ghost btn-sm" target="_blank">🏷️ Etiqueta</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
