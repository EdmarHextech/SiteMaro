<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/carrinho.php';
iniciar_sessao();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (csrf_validar($_POST['csrf_token'] ?? null)) {
        $acao = $_POST['acao'] ?? '';
        $produtoId = (int) ($_POST['produto_id'] ?? 0);
        if ($acao === 'adicionar' && $produtoId > 0) {
            $qtd = max(1, (int) ($_POST['quantidade'] ?? 1));
            $dedicatoria = $_POST['dedicatoria_texto'] ?? null;
            carrinho_adicionar($produtoId, $qtd, $dedicatoria, $erroCarrinho);
        } elseif ($acao === 'atualizar' && $produtoId > 0) {
            carrinho_atualizar_quantidade($produtoId, (int) ($_POST['quantidade'] ?? 0));
        } elseif ($acao === 'remover' && $produtoId > 0) {
            carrinho_remover($produtoId);
        }
    }
    header('Location: /carrinho.php');
    exit;
}

$page_title = t('carrinho.title');
require __DIR__ . '/includes/header.php';

$itens = carrinho_conteudo();
$subtotal = carrinho_subtotal_centavos();
$tipoCarrinho = carrinho_tipo();
?>

<section class="hero" style="padding:70px 0 50px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker"><?= e(t('carrinho.hero.kicker')) ?></p>
      <h1><?= e(t('carrinho.hero.title')) ?></h1>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="container" style="max-width:760px;">
    <?php if (empty($itens)): ?>
      <div class="agenda-empty">
        <p><?= e(t('carrinho.empty')) ?></p>
        <p style="margin-top:16px;"><a href="/loja.php" class="btn btn-teal"><?= e(t('carrinho.cta_loja')) ?></a></p>
      </div>
    <?php else: ?>
      <div class="card" style="padding:0; overflow:hidden;">
        <table class="admin-table">
          <thead>
            <tr>
              <th><?= e(t('carrinho.col.produto')) ?></th>
              <th><?= e(t('carrinho.col.qtd')) ?></th>
              <th><?= e(t('carrinho.col.subtotal')) ?></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($itens as $item): ?>
              <tr>
                <td>
                  <?= e($item['produto']['nome']) ?>
                  <?php if (!empty($item['dedicatoria_texto'])): ?>
                    <br><span class="form-hint">✒️ "<?= e($item['dedicatoria_texto']) ?>"</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($item['produto']['tipo'] === 'sessao'): ?>
                    1
                  <?php else: ?>
                    <form method="post" style="display:flex; gap:6px; align-items:center;">
                      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                      <input type="hidden" name="acao" value="atualizar">
                      <input type="hidden" name="produto_id" value="<?= (int) $item['produto']['id'] ?>">
                      <input type="number" name="quantidade" min="1" max="20" value="<?= (int) $item['quantidade'] ?>" class="form-control" style="width:70px; padding:8px;">
                      <button type="submit" class="btn btn-ghost btn-sm"><?= e(t('carrinho.atualizar')) ?></button>
                    </form>
                  <?php endif; ?>
                </td>
                <td><?= e(formatar_preco($item['subtotal_centavos'])) ?></td>
                <td>
                  <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="acao" value="remover">
                    <input type="hidden" name="produto_id" value="<?= (int) $item['produto']['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm"><?= e(t('carrinho.remover')) ?></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div style="display:flex; justify-content:space-between; align-items:center; margin-top:24px;">
        <p style="font-size:1.2rem; font-weight:700; color:var(--text-heading); margin:0;">
          <?= e(t('carrinho.subtotal')) ?>: <?= e(formatar_preco($subtotal)) ?>
        </p>
        <a href="/checkout.php" class="btn btn-teal"><?= e(t('carrinho.finalizar')) ?></a>
      </div>
      <?php if ($tipoCarrinho === 'fisico'): ?>
        <p class="form-hint" style="text-align:right; margin-top:6px;"><?= e(t('carrinho.frete_aviso')) ?></p>
      <?php else: ?>
        <p class="form-hint" style="text-align:right; margin-top:6px;"><?= e(t('carrinho.agendamento_aviso')) ?></p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
