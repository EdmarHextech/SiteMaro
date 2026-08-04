<?php
/** @var array $produto */
?>
<?php $esgotado = $produto['estoque'] !== null && (int) $produto['estoque'] <= 0; ?>
<a href="/produto.php?slug=<?= e($produto['slug']) ?>" class="card produto-card<?= $esgotado ? ' produto-card--esgotado' : '' ?>">
  <?php if (!empty($produto['imagem'])): ?>
    <img src="<?= e($produto['imagem']) ?>" alt="<?= e($produto['nome']) ?>" class="produto-card__img" loading="lazy">
  <?php endif; ?>
  <div class="produto-card__body">
    <h3><?= e($produto['nome']) ?></h3>
    <p class="produto-card__preco"><?= e(formatar_preco((int) $produto['preco_centavos'])) ?></p>
    <?php if ($esgotado): ?><span class="produto-card__badge">Esgotado</span><?php endif; ?>
  </div>
</a>
