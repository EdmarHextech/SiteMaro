<?php
require_once __DIR__ . '/../includes/functions.php';
exigir_login();

$id = (int) ($_GET['id'] ?? 0);
$pedido = buscar_pedido($id);
if (!$pedido || empty($pedido['endereco_cep'])) {
    header('Location: /admin/pedidos.php');
    exit;
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Etiqueta — Pedido <?= e($pedido['codigo']) ?></title>
<meta name="robots" content="noindex, nofollow">
<style>
  * { box-sizing: border-box; }
  body { font-family: Arial, Helvetica, sans-serif; background: #eee; margin: 0; padding: 32px; color: #111; }
  .toolbar { max-width: 760px; margin: 0 auto 20px; display: flex; justify-content: space-between; align-items: center; }
  .toolbar a, .toolbar button {
    font-family: Arial, sans-serif; font-size: 0.9rem; padding: 10px 18px; border-radius: 999px;
    border: 1.5px solid #146c6d; background: #146c6d; color: #fff; cursor: pointer; text-decoration: none;
  }
  .toolbar a { background: transparent; color: #146c6d; }
  .folha { max-width: 760px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }
  .etiqueta {
    background: #fff; border: 2px solid #111; border-radius: 4px; padding: 20px 24px;
  }
  .etiqueta__tipo {
    font-size: 0.8rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
    border-bottom: 2px solid #111; padding-bottom: 8px; margin-bottom: 12px;
  }
  .etiqueta__nome { font-size: 1.15rem; font-weight: 700; margin-bottom: 6px; }
  .etiqueta__linha { font-size: 1rem; margin-bottom: 3px; }
  .etiqueta__cep {
    display: inline-block; margin-top: 12px; font-size: 1.4rem; font-weight: 700;
    letter-spacing: 0.04em; border: 2px solid #111; padding: 6px 14px; border-radius: 4px;
  }
  .etiqueta__ref { margin-top: 14px; font-size: 0.8rem; color: #444; }
  .aviso { max-width: 760px; margin: 0 auto 20px; background: #fff3cd; border: 1px solid #ffe08a; border-radius: 6px; padding: 12px 16px; font-size: 0.88rem; }
  @media print {
    body { background: #fff; padding: 0; }
    .toolbar, .aviso { display: none; }
    .folha { gap: 12mm; }
    .etiqueta { break-inside: avoid; border-width: 1.5px; }
  }
</style>
</head>
<body>

<div class="toolbar">
  <a href="/admin/pedido-detalhe.php?id=<?= (int) $pedido['id'] ?>">← Voltar para o pedido</a>
  <button type="button" onclick="window.print()">Imprimir</button>
</div>

<?php if (!remetente_configurado()): ?>
  <div class="aviso">
    Endereço do remetente não configurado ainda — a etiqueta de remetente abaixo está incompleta.
    Preencha <code>REMETENTE_LOGRADOURO</code>, <code>REMETENTE_CIDADE</code>, <code>REMETENTE_UF</code>, <code>REMETENTE_CEP</code> etc. em <code>includes/config.local.php</code>.
  </div>
<?php endif; ?>

<div class="folha">
  <div class="etiqueta">
    <div class="etiqueta__tipo">Remetente</div>
    <div class="etiqueta__nome"><?= e(REMETENTE_NOME) ?></div>
    <div class="etiqueta__linha">
      <?= e(REMETENTE_LOGRADOURO ?: '—') ?><?= REMETENTE_NUMERO !== '' ? ', ' . e(REMETENTE_NUMERO) : '' ?>
      <?= REMETENTE_COMPLEMENTO !== '' ? ' — ' . e(REMETENTE_COMPLEMENTO) : '' ?>
    </div>
    <div class="etiqueta__linha"><?= e(REMETENTE_BAIRRO) ?></div>
    <div class="etiqueta__linha"><?= e(REMETENTE_CIDADE) ?> <?= REMETENTE_UF !== '' ? '- ' . e(REMETENTE_UF) : '' ?></div>
    <div class="etiqueta__cep">CEP <?= e(formatar_cep(REMETENTE_CEP)) ?></div>
  </div>

  <div class="etiqueta">
    <div class="etiqueta__tipo">Destinatário</div>
    <div class="etiqueta__nome"><?= e($pedido['cliente_nome']) ?></div>
    <div class="etiqueta__linha">
      <?= e($pedido['endereco_logradouro'] ?? '') ?><?= !empty($pedido['endereco_numero']) ? ', ' . e($pedido['endereco_numero']) : '' ?>
      <?= !empty($pedido['endereco_complemento']) ? ' — ' . e($pedido['endereco_complemento']) : '' ?>
    </div>
    <div class="etiqueta__linha"><?= e($pedido['endereco_bairro'] ?? '') ?></div>
    <div class="etiqueta__linha"><?= e($pedido['endereco_cidade'] ?? '') ?> <?= !empty($pedido['endereco_uf']) ? '- ' . e($pedido['endereco_uf']) : '' ?></div>
    <div class="etiqueta__cep">CEP <?= e(formatar_cep($pedido['endereco_cep'])) ?></div>
    <div class="etiqueta__ref">Pedido <?= e($pedido['codigo']) ?><?= !empty($pedido['cliente_telefone']) ? ' · Tel: ' . e($pedido['cliente_telefone']) : '' ?></div>
  </div>
</div>

</body>
</html>
