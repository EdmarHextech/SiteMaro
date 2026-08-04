<?php
require_once __DIR__ . '/../includes/functions.php';
exigir_login();

$id = (int) ($_GET['id'] ?? 0);
$pedido = buscar_pedido($id);
if (!$pedido) {
    header('Location: /admin/pedidos.php');
    exit;
}

$statusValidos = ['pendente', 'pago', 'recusado', 'cancelado', 'reembolsado'];
$erro = null;
$msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validar($_POST['csrf_token'] ?? null)) {
        $erro = 'Sessão expirada. Atualize a página e tente novamente.';
    } else {
        $novoStatus = $_POST['status'] ?? '';
        if (in_array($novoStatus, $statusValidos, true)) {
            $stmt = db()->prepare('UPDATE pedidos SET status = ? WHERE id = ?');
            $stmt->execute([$novoStatus, $id]);
            $pedido = buscar_pedido($id);
            $msg = 'Status atualizado manualmente para "' . $novoStatus . '".';
        }
    }
}

$itens = buscar_itens_pedido($id);
$admin_page_title = 'Pedido ' . $pedido['codigo'];
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <h1>Pedido <?= e($pedido['codigo']) ?></h1>
  <a href="/admin/pedidos.php" class="btn btn-ghost">← Voltar para pedidos</a>
</div>

<?php if ($erro): ?><div class="alert alert-error"><?= e($erro) ?></div><?php endif; ?>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="grid grid-2" style="align-items:start;">
  <div class="card">
    <h3 style="font-size:1rem;">Cliente</h3>
    <p><?= e($pedido['cliente_nome']) ?><br>
    <?= e($pedido['cliente_email']) ?><br>
    <?= e($pedido['cliente_telefone'] ?? '—') ?><br>
    CPF: <?= e($pedido['cliente_cpf'] ?? '—') ?></p>

    <h3 style="font-size:1rem; margin-top:20px;">Endereço de entrega</h3>
    <p>
      <?= e($pedido['endereco_logradouro'] ?? '') ?>, <?= e($pedido['endereco_numero'] ?? '') ?>
      <?= !empty($pedido['endereco_complemento']) ? ' — ' . e($pedido['endereco_complemento']) : '' ?><br>
      <?= e($pedido['endereco_bairro'] ?? '') ?> — <?= e($pedido['endereco_cidade'] ?? '') ?>/<?= e($pedido['endereco_uf'] ?? '') ?><br>
      CEP: <?= e($pedido['endereco_cep'] ?? '—') ?>
    </p>

    <h3 style="font-size:1rem; margin-top:20px;">Itens</h3>
    <?php foreach ($itens as $item): ?>
      <div style="display:flex; justify-content:space-between; padding:4px 0; font-size:0.92rem; color:var(--text-soft);">
        <span>
          <?= (int) $item['quantidade'] ?>x <?= e($item['produto_nome_snapshot']) ?>
          <?php if (!empty($item['dedicatoria_texto'])): ?><br><em>✒️ "<?= e($item['dedicatoria_texto']) ?>"</em><?php endif; ?>
        </span>
        <span><?= e(formatar_preco((int) $item['preco_unitario_centavos'] * (int) $item['quantidade'])) ?></span>
      </div>
    <?php endforeach; ?>
    <div style="display:flex; justify-content:space-between; padding-top:10px; border-top:1px solid var(--border-soft); margin-top:8px;">
      <span>Frete<?= !empty($pedido['frete_servico']) ? ' (' . e($pedido['frete_servico']) . ')' : '' ?></span><span><?= e(formatar_preco((int) $pedido['frete_centavos'])) ?></span>
    </div>
    <div style="display:flex; justify-content:space-between; font-weight:700; color:var(--text-heading);">
      <span>Total</span><span><?= e(formatar_preco((int) $pedido['total_centavos'])) ?></span>
    </div>
  </div>

  <div class="card">
    <h3 style="font-size:1rem;">Pagamento</h3>
    <p>
      Método: <?= e($pedido['metodo_pagamento'] ?? '—') ?><br>
      ID Mercado Pago: <?= e($pedido['mp_payment_id'] ?? '—') ?><br>
      Detalhe: <?= e($pedido['mp_status_detail'] ?? '—') ?><br>
      Criado em: <?= e(date('d/m/Y H:i', strtotime($pedido['created_at']))) ?>
    </p>

    <h3 style="font-size:1rem; margin-top:20px;">Status atual: <span class="status-pill <?= $pedido['status'] === 'pago' ? 'ativo' : 'inativo' ?>"><?= e($pedido['status']) ?></span></h3>
    <form method="post" style="margin-top:12px;">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <div class="form-group">
        <label for="status">Alterar status manualmente</label>
        <select class="form-control" id="status" name="status" style="max-width:220px;">
          <?php foreach ($statusValidos as $s): ?>
            <option value="<?= e($s) ?>" <?= $pedido['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <p class="form-hint">Use apenas em casos excepcionais (ex: reembolso combinado por fora, pedido cancelado por telefone). O status normal é atualizado automaticamente pelo Mercado Pago.</p>
      <button type="submit" class="btn btn-teal btn-sm">Salvar status</button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
