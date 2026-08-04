<?php
// Sitemap dinâmico: páginas estáticas + posts publicados do blog (atualiza sozinho a cada novo post).
// Servido em /sitemap.xml via rewrite no .htaccess.
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$base = rtrim(SITE_URL, '/');

$paginasEstaticas = [
    ['loc' => '/index.php', 'changefreq' => 'weekly', 'priority' => '1.0'],
    ['loc' => '/sobre.php', 'changefreq' => 'monthly', 'priority' => '0.7'],
    ['loc' => '/livro.php', 'changefreq' => 'monthly', 'priority' => '0.7'],
    ['loc' => '/palestras-consultorias.php', 'changefreq' => 'monthly', 'priority' => '0.7'],
    ['loc' => '/agenda.php', 'changefreq' => 'daily', 'priority' => '0.9'],
    ['loc' => '/agende-um-horario.php', 'changefreq' => 'monthly', 'priority' => '0.6'],
    ['loc' => '/blog.php', 'changefreq' => 'weekly', 'priority' => '0.8'],
    ['loc' => '/loja.php', 'changefreq' => 'weekly', 'priority' => '0.6'],
    ['loc' => '/galeria.php', 'changefreq' => 'monthly', 'priority' => '0.5'],
    ['loc' => '/contato.php', 'changefreq' => 'monthly', 'priority' => '0.6'],
];

$posts = db()->query("SELECT slug, updated_at FROM posts WHERE status = 'publicado' ORDER BY publicado_em DESC")->fetchAll();
$produtos = db()->query("SELECT slug, updated_at FROM produtos WHERE ativo = 1 ORDER BY updated_at DESC")->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($paginasEstaticas as $p): ?>
  <url>
    <loc><?= e($base . $p['loc']) ?></loc>
    <changefreq><?= e($p['changefreq']) ?></changefreq>
    <priority><?= e($p['priority']) ?></priority>
  </url>
<?php endforeach; ?>
<?php foreach ($posts as $post): ?>
  <url>
    <loc><?= e($base . '/blog-post.php?slug=' . rawurlencode($post['slug'])) ?></loc>
    <lastmod><?= e(date('Y-m-d', strtotime($post['updated_at']))) ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
<?php endforeach; ?>
<?php foreach ($produtos as $produto): ?>
  <url>
    <loc><?= e($base . '/produto.php?slug=' . rawurlencode($produto['slug'])) ?></loc>
    <lastmod><?= e(date('Y-m-d', strtotime($produto['updated_at']))) ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
  </url>
<?php endforeach; ?>
</urlset>
