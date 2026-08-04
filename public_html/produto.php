<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/carrinho.php';
iniciar_sessao();

$slug = $_GET['slug'] ?? '';
$produto = $slug !== '' ? buscar_produto_por_slug($slug) : null;

if (!$produto) {
    http_response_code(404);
    $page_title = t('loja.not_found.title');
    require __DIR__ . '/includes/header.php';
    ?>
    <section class="section" style="text-align:center;">
      <div class="container">
        <h1><?= e(t('loja.not_found.title')) ?></h1>
        <p><a href="/loja.php" class="btn btn-teal"><?= e(t('loja.not_found.cta')) ?></a></p>
      </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$urlCanonica = rtrim(SITE_URL, '/') . '/produto.php?slug=' . rawurlencode($produto['slug']);
$page_title = $produto['nome'] . ' — Loja Maro Camargo';
$page_description = $produto['descricao'] ? mb_substr(strip_tags($produto['descricao']), 0, 200) : t('loja.description');
$page_canonical = $urlCanonica;
if (!empty($produto['imagem'])) {
    $page_og_image = rtrim(SITE_URL, '/') . $produto['imagem'];
}
require __DIR__ . '/includes/header.php';

$mensagemWhatsapp = t('loja.produto.whatsapp_msg') . ' ' . $produto['nome'] . ' (' . $urlCanonica . ')';
?>

<section class="section" style="padding-top:60px;">
  <div class="container book-grid" style="align-items:start;">
    <div class="book-cover">
      <?php if (!empty($produto['imagem'])): ?>
        <img src="<?= e($produto['imagem']) ?>" alt="<?= e($produto['nome']) ?>">
      <?php endif; ?>
    </div>
    <div>
      <p class="eyebrow"><?= $produto['tipo'] === 'fisico' ? e(t('loja.section.livro')) : e(t('loja.section.sessoes')) ?></p>
      <h1><?= e($produto['nome']) ?></h1>
      <p class="lead" style="color:var(--text-heading); font-weight:700; font-size:1.4rem;"><?= e(formatar_preco((int) $produto['preco_centavos'])) ?></p>

      <?php if (!empty($produto['descricao'])): ?>
        <p style="white-space:pre-line;"><?= e($produto['descricao']) ?></p>
      <?php endif; ?>

      <?php if ($produto['tipo'] === 'sessao' && !empty($produto['duracao_minutos'])): ?>
        <p class="form-hint">⏱ <?= (int) $produto['duracao_minutos'] ?> <?= e(t('loja.produto.minutos')) ?></p>
      <?php endif; ?>

      <?php if ($produto['tipo'] === 'fisico' && $produto['permite_dedicatoria']): ?>
        <p class="form-hint">✒️ <?= e(t('loja.produto.dedicatoria')) ?></p>
      <?php endif; ?>

      <?php if ($produto['tipo'] === 'fisico'): ?>
        <form method="post" action="/carrinho.php" style="margin-top:24px; padding:22px 24px; background:var(--bg-tint); border-radius:var(--radius-md);">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="acao" value="adicionar">
          <input type="hidden" name="produto_id" value="<?= (int) $produto['id'] ?>">
          <?php if ($produto['permite_dedicatoria']): ?>
            <div class="form-group">
              <label for="dedicatoria_texto"><?= e(t('loja.produto.dedicatoria_label')) ?></label>
              <input class="form-control" type="text" id="dedicatoria_texto" name="dedicatoria_texto" maxlength="500" placeholder="<?= e(t('loja.produto.dedicatoria_placeholder')) ?>">
            </div>
          <?php endif; ?>
          <div class="form-row" style="align-items:end;">
            <div class="form-group" style="margin-bottom:0;">
              <label for="quantidade"><?= e(t('carrinho.col.qtd')) ?></label>
              <input class="form-control" type="number" id="quantidade" name="quantidade" min="1" max="20" value="1" style="width:100px;">
            </div>
            <button type="submit" class="btn btn-teal" style="height:fit-content;"><?= e(t('loja.produto.cta_carrinho')) ?></button>
          </div>
          <?php if (!mp_configurado()): ?>
            <p class="form-hint" style="margin-top:14px;"><?= e(t('loja.produto.checkout_em_breve')) ?></p>
          <?php endif; ?>
        </form>
        <p style="margin-top:14px;">
          <?= e(t('loja.produto.ou_fale')) ?>
          <a href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>?text=<?= urlencode($mensagemWhatsapp) ?>" target="_blank" rel="noopener"><?= e(t('loja.produto.cta_whatsapp')) ?></a>
        </p>
      <?php else: ?>
        <div class="coming-soon" style="padding:24px 26px; margin-top:24px;">
          <span class="coming-soon__badge"><?= e(t('common.coming_soon')) ?></span>
          <p style="margin:12px 0 18px;"><?= e(t('loja.produto.checkout_em_breve')) ?></p>
          <a href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>?text=<?= urlencode($mensagemWhatsapp) ?>" target="_blank" rel="noopener" class="btn btn-teal"><?= e(t('loja.produto.cta_whatsapp')) ?></a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
