<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/mercadopago.php';

$codigo = $_GET['codigo'] ?? '';
$pedido = $codigo !== '' ? buscar_pedido_por_codigo($codigo) : null;

$page_title = t('pedido.confirmado.title');
require __DIR__ . '/includes/header.php';

if (!$pedido) {
    ?>
    <section class="section" style="text-align:center;">
      <div class="container">
        <h1><?= e(t('pedido.nao_encontrado')) ?></h1>
        <p><a href="/loja.php" class="btn btn-teal"><?= e(t('loja.not_found.cta')) ?></a></p>
      </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$itens = buscar_itens_pedido((int) $pedido['id']);

$pixQrCode = null;
$pixCopiaCola = null;
if ($pedido['metodo_pagamento'] === 'pix' && $pedido['status'] === 'pendente' && $pedido['mp_payment_id'] && mp_configurado()) {
    try {
        $detalhe = mp_consultar_pagamento($pedido['mp_payment_id']);
        $pixQrCode = $detalhe['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null;
        $pixCopiaCola = $detalhe['point_of_interaction']['transaction_data']['qr_code'] ?? null;
    } catch (MercadoPagoException $e) {
        error_log('[pedido-confirmado] Falha ao consultar Pix do pedido ' . $pedido['codigo'] . ': ' . $e->getMessage());
    }
}
?>

<section class="section" style="padding-top:70px;">
  <div class="container" style="max-width:640px; text-align:center;">
    <?php if ($pedido['status'] === 'pago'): ?>
      <div class="alert alert-success"><?= e(t('pedido.status.pago')) ?></div>
    <?php elseif ($pedido['status'] === 'recusado'): ?>
      <div class="alert alert-error"><?= e(t('pedido.status.recusado')) ?></div>
    <?php else: ?>
      <div class="alert alert-success"><?= e(t('pedido.status.pendente')) ?></div>
    <?php endif; ?>

    <h1><?= e(t('pedido.confirmado.title')) ?></h1>
    <p class="lead"><?= e(t('pedido.codigo')) ?>: <strong><?= e($pedido['codigo']) ?></strong></p>

    <?php if ($pixQrCode): ?>
      <div class="card" style="margin:24px 0;">
        <h3 style="font-size:1.05rem;"><?= e(t('pedido.pix.title')) ?></h3>
        <img src="data:image/png;base64,<?= e($pixQrCode) ?>" alt="QR Code Pix" style="max-width:240px; margin:16px auto;">
        <?php if ($pixCopiaCola): ?>
          <p class="form-hint"><?= e(t('pedido.pix.copia_cola')) ?></p>
          <textarea class="form-control" rows="3" readonly onclick="this.select()"><?= e($pixCopiaCola) ?></textarea>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="card" style="margin-top:24px; text-align:left;">
      <?php foreach ($itens as $item): ?>
        <div style="display:flex; justify-content:space-between; padding:6px 0; color:var(--text-soft);">
          <span><?= (int) $item['quantidade'] ?>x <?= e($item['produto_nome_snapshot']) ?></span>
          <span><?= e(formatar_preco((int) $item['preco_unitario_centavos'] * (int) $item['quantidade'])) ?></span>
        </div>
      <?php endforeach; ?>
      <div style="display:flex; justify-content:space-between; padding:6px 0; color:var(--text-soft); border-top:1px solid var(--border-soft); margin-top:8px;">
        <span><?= e(t('checkout.frete')) ?><?= !empty($pedido['frete_servico']) ? ' (' . e($pedido['frete_servico']) . ')' : '' ?></span>
        <span><?= e(formatar_preco((int) $pedido['frete_centavos'])) ?></span>
      </div>
      <div style="display:flex; justify-content:space-between; font-weight:700; color:var(--text-heading); padding-top:10px;">
        <span><?= e(t('checkout.total')) ?></span>
        <span><?= e(formatar_preco((int) $pedido['total_centavos'])) ?></span>
      </div>
    </div>

    <p style="margin-top:24px;"><a href="/loja.php" class="btn btn-ghost"><?= e(t('pedido.voltar_loja')) ?></a></p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
