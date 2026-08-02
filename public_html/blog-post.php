<?php
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$post = $slug !== '' ? buscar_post_por_slug($slug) : null;

if (!$post) {
    http_response_code(404);
    $page_title = t('blog.not_found.title');
    require __DIR__ . '/includes/header.php';
    ?>
    <section class="section" style="text-align:center;">
      <div class="container">
        <h1><?= e(t('blog.not_found.title')) ?></h1>
        <p><a href="/blog.php" class="btn btn-teal"><?= e(t('blog.not_found.cta')) ?></a></p>
      </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$urlCanonica = rtrim(SITE_URL, '/') . '/blog-post.php?slug=' . rawurlencode($post['slug']);
$imagemOg = !empty($post['imagem_capa']) ? rtrim(SITE_URL, '/') . $post['imagem_capa'] : null;

$page_title = $post['titulo'] . ' — Blog Maro Camargo';
$page_description = $post['resumo'] ?: mb_substr(trim(strip_tags($post['conteudo_html'])), 0, 200);
$page_canonical = $urlCanonica;
if ($imagemOg) {
    $page_og_image = $imagemOg;
}
require __DIR__ . '/includes/header.php';
?>

<article class="section" style="padding-top:60px;">
  <div class="container" style="max-width:760px;">
    <p class="eyebrow"><?= e(t('blog.eyebrow')) ?></p>
    <h1><?= e($post['titulo']) ?></h1>
    <p style="color:var(--text-soft); margin-bottom:28px;">
      <?= e($post['autor']) ?> · <?= e(date('d/m/Y', strtotime($post['publicado_em']))) ?>
    </p>

    <?php if (!empty($post['imagem_capa'])): ?>
      <img src="<?= e($post['imagem_capa']) ?>" alt="<?= e($post['titulo']) ?>" style="width:100%; border-radius:var(--radius-md); box-shadow:var(--shadow-card); margin-bottom:32px;">
    <?php endif; ?>

    <div class="blog-content">
      <?= $post['conteudo_html'] ?>
    </div>

    <div class="blog-share">
      <p class="blog-share__label"><?= e(t('blog.share.label')) ?></p>
      <a href="https://wa.me/?text=<?= urlencode($post['titulo'] . ' — ' . $urlCanonica) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">WhatsApp</a>
      <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($urlCanonica) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">Facebook</a>
      <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($urlCanonica) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">LinkedIn</a>
      <a href="https://twitter.com/intent/tweet?url=<?= urlencode($urlCanonica) ?>&text=<?= urlencode($post['titulo']) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">X</a>
    </div>
  </div>
</article>

<?php require __DIR__ . '/includes/footer.php'; ?>
