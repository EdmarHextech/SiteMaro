<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = t('blog.title');
$page_description = t('blog.description');
require __DIR__ . '/includes/header.php';

$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$porPagina = 9;
$posts = buscar_posts_publicados($pagina, $porPagina);
$total = contar_posts_publicados();
$totalPaginas = (int) max(1, ceil($total / $porPagina));
?>

<section class="hero" style="padding:90px 0 60px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker"><?= e(t('blog.hero.kicker')) ?></p>
      <h1><?= e(t('blog.hero.title')) ?></h1>
      <p class="lead" style="margin:0 auto;"><?= e(t('blog.hero.lead')) ?></p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (empty($posts)): ?>
      <div class="agenda-empty">
        <h3><?= e(t('blog.empty.title')) ?></h3>
        <p><?= e(t('blog.empty.desc')) ?></p>
      </div>
    <?php else: ?>
      <div class="grid grid-3">
        <?php foreach ($posts as $post): ?>
          <a href="/blog-post.php?slug=<?= e($post['slug']) ?>" class="card blog-card">
            <?php if (!empty($post['imagem_capa'])): ?>
              <img src="<?= e($post['imagem_capa']) ?>" alt="<?= e($post['titulo']) ?>" class="blog-card__img" loading="lazy">
            <?php endif; ?>
            <div class="blog-card__body">
              <h3><?= e($post['titulo']) ?></h3>
              <?php if (!empty($post['resumo'])): ?>
                <p><?= e($post['resumo']) ?></p>
              <?php endif; ?>
              <span class="blog-card__date"><?= e(date('d/m/Y', strtotime($post['publicado_em']))) ?></span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>

      <?php if ($totalPaginas > 1): ?>
        <div class="blog-pagination">
          <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
            <a href="/blog.php?pagina=<?= $p ?>" class="<?= $p === $pagina ? 'is-active' : '' ?>"><?= $p ?></a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
