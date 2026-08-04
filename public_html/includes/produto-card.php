<?php
/** @var array $produto */
?>
<a href="/produto.php?slug=<?= e($produto['slug']) ?>" class="card produto-card">
  <?php if (!empty($produto['imagem'])): ?>
    <img src="<?= e($produto['imagem']) ?>" alt="<?= e($produto['nome']) ?>" class="produto-card__img" loading="lazy">
  <?php endif; ?>
  <div class="produto-card__body">
    <h3><?= e($produto['nome']) ?></h3>
    <p class="produto-card__preco"><?= e(formatar_preco((int) $produto['preco_centavos'])) ?></p>
  </div>
</a>
