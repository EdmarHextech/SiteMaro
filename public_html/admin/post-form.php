<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/uploads.php';
require_once __DIR__ . '/../includes/sanitizador.php';
exigir_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : null);
$post = $id ? buscar_post($id) : null;
if ($id && !$post) {
    header('Location: /admin/posts.php');
    exit;
}

$erro = null;
$valores = $post ?? [
    'titulo' => '', 'slug' => '', 'resumo' => '', 'conteudo_html' => '',
    'imagem_capa' => null, 'status' => 'rascunho', 'publicado_em' => null,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validar($_POST['csrf_token'] ?? null)) {
        $erro = 'Sessão expirada. Atualize a página e tente novamente.';
    } else {
        $valores = [
            'titulo' => trim($_POST['titulo'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'resumo' => trim($_POST['resumo'] ?? ''),
            'conteudo_html' => (string) ($_POST['conteudo_html'] ?? ''),
            'imagem_capa' => $valores['imagem_capa'], // mantém a atual até processarmos um novo upload
            'status' => in_array($_POST['status'] ?? '', ['rascunho', 'publicado'], true) ? $_POST['status'] : 'rascunho',
            'publicado_em' => $valores['publicado_em'],
        ];

        // Upload de nova capa (opcional em edição, obrigatório antes de publicar).
        $novaCapa = null;
        if (!empty($_FILES['imagem_capa']['name'])) {
            $novaCapa = salvar_imagem_enviada($_FILES['imagem_capa'], 'blog', $erroUpload);
            if ($novaCapa === null) {
                $erro = $erroUpload;
            }
        }

        if (!$erro && $valores['titulo'] === '') {
            $erro = 'Título é obrigatório.';
        } elseif (!$erro && trim(strip_tags($valores['conteudo_html'])) === '') {
            $erro = 'O conteúdo do post não pode ficar vazio.';
        } else {
            $capaFinal = $novaCapa ?? $valores['imagem_capa'];
            if (!$erro && $valores['status'] === 'publicado' && !$capaFinal) {
                $erro = 'Para publicar, envie uma imagem de capa (ela também é usada como imagem de compartilhamento).';
            }
        }

        if (!$erro) {
            $slugBase = $valores['slug'] !== '' ? $valores['slug'] : $valores['titulo'];
            $slugFinal = gerar_slug_unico_post($slugBase, $id);
            $conteudoSanitizado = sanitizar_html_rico($valores['conteudo_html']);
            $resumoFinal = $valores['resumo'] !== '' ? $valores['resumo'] : null;
            $capaFinal = $novaCapa ?? $valores['imagem_capa'];
            $publicadoEm = $valores['publicado_em'];
            if ($valores['status'] === 'publicado' && !$publicadoEm) {
                $publicadoEm = date('Y-m-d H:i:s');
            }

            if ($id) {
                if ($novaCapa && $valores['imagem_capa']) {
                    remover_imagem_enviada($valores['imagem_capa']);
                }
                $stmt = db()->prepare('UPDATE posts SET titulo=?, slug=?, resumo=?, conteudo_html=?, imagem_capa=?, status=?, publicado_em=? WHERE id=?');
                $stmt->execute([$valores['titulo'], $slugFinal, $resumoFinal, $conteudoSanitizado, $capaFinal, $valores['status'], $publicadoEm, $id]);
                header('Location: /admin/posts.php?msg=atualizado');
            } else {
                $stmt = db()->prepare('INSERT INTO posts (titulo, slug, resumo, conteudo_html, imagem_capa, status, publicado_em) VALUES (?,?,?,?,?,?,?)');
                $stmt->execute([$valores['titulo'], $slugFinal, $resumoFinal, $conteudoSanitizado, $capaFinal, $valores['status'], $publicadoEm]);
                header('Location: /admin/posts.php?msg=criado');
            }
            exit;
        }
    }
}

$admin_page_title = $id ? 'Editar post' : 'Novo post';
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <h1><?= $id ? 'Editar post' : 'Novo post' ?></h1>
  <a href="/admin/posts.php" class="btn btn-ghost">← Voltar para o blog</a>
</div>

<?php if ($erro): ?><div class="alert alert-error"><?= e($erro) ?></div><?php endif; ?>

<div class="card" style="max-width:900px;">
  <form method="post" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <?php if ($id): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>

    <div class="form-group">
      <label for="titulo">Título</label>
      <input class="form-control" type="text" id="titulo" name="titulo" required value="<?= e($valores['titulo']) ?>" placeholder="Título do post">
    </div>

    <div class="form-group">
      <label for="slug">Endereço (slug)</label>
      <input class="form-control" type="text" id="slug" name="slug" value="<?= e($valores['slug']) ?>" placeholder="gerado automaticamente a partir do título se deixado em branco">
      <p class="form-hint">Aparece na URL: /blog-post.php?slug=<em>seu-slug</em>. Deixe em branco para gerar a partir do título.</p>
    </div>

    <div class="form-group">
      <label for="resumo">Resumo curto (opcional)</label>
      <textarea class="form-control" id="resumo" name="resumo" rows="2" maxlength="500"><?= e($valores['resumo']) ?></textarea>
      <p class="form-hint">Usado como descrição ao compartilhar o post (e como chamada na listagem do blog).</p>
    </div>

    <div class="form-group">
      <label for="imagem_capa">Imagem de capa <?= $valores['status'] === 'publicado' || !$id ? '(obrigatória para publicar)' : '' ?></label>
      <?php if (!empty($valores['imagem_capa'])): ?>
        <div style="margin-bottom:10px;"><img src="<?= e($valores['imagem_capa']) ?>" alt="Capa atual" style="max-width:260px; border-radius:var(--radius-sm);"></div>
      <?php endif; ?>
      <input class="form-control" type="file" id="imagem_capa" name="imagem_capa" accept="image/jpeg,image/png,image/webp">
      <p class="form-hint">É a imagem usada quando o post é compartilhado no WhatsApp, Facebook, LinkedIn etc. JPG, PNG ou WEBP, até 5MB.</p>
    </div>

    <div class="form-group">
      <label for="conteudo_html">Conteúdo</label>
      <textarea id="conteudo_html" name="conteudo_html"><?= $valores['conteudo_html'] ?></textarea>
    </div>

    <div class="form-group">
      <label for="status">Status</label>
      <select class="form-control" id="status" name="status" style="max-width:220px;">
        <option value="rascunho" <?= $valores['status'] === 'rascunho' ? 'selected' : '' ?>>Rascunho</option>
        <option value="publicado" <?= $valores['status'] === 'publicado' ? 'selected' : '' ?>>Publicado</option>
      </select>
    </div>

    <button type="submit" class="btn btn-teal"><?= $id ? 'Salvar alterações' : 'Criar post' ?></button>
  </form>
</div>

<script src="/assets/vendor/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
<script>
(function () {
  var csrfToken = <?= json_encode(csrf_token()) ?>;
  var isDark = document.documentElement.getAttribute('data-theme') === 'dark';

  tinymce.init({
    selector: '#conteudo_html',
    height: 480,
    menubar: false,
    base_url: '/assets/vendor/tinymce',
    suffix: '.min',
    license_key: 'gpl',
    skin: isDark ? 'oxide-dark' : 'oxide',
    content_css: isDark ? 'dark' : 'default',
    plugins: 'link image lists advlist autolink wordcount charmap searchreplace table fullscreen help code',
    toolbar: 'undo redo | blocks fontfamily | bold italic underline forecolor backcolor | bullist numlist | link image table | alignleft aligncenter alignright | code fullscreen help',
    font_family_formats: 'Inter=Inter,Segoe UI,sans-serif; Poppins=Poppins,Segoe UI,sans-serif',
    block_formats: 'Parágrafo=p; Título 2=h2; Título 3=h3; Citação=blockquote',
    branding: false,
    promotion: false,
    images_upload_handler: function (blobInfo) {
      return new Promise(function (resolve, reject) {
        var formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        formData.append('csrf_token', csrfToken);
        fetch('/admin/upload-imagem.php', { method: 'POST', body: formData, credentials: 'same-origin' })
          .then(function (r) { return r.json(); })
          .then(function (json) {
            if (json.location) { resolve(json.location); }
            else { reject((json.error && json.error.message) || 'Falha no upload.'); }
          })
          .catch(function () { reject('Falha no upload.'); });
      });
    },
  });

  var tituloInput = document.getElementById('titulo');
  var slugInput = document.getElementById('slug');
  var slugTocadoManualmente = slugInput.value !== '';
  slugInput.addEventListener('input', function () { slugTocadoManualmente = true; });
  tituloInput.addEventListener('input', function () {
    if (slugTocadoManualmente) return;
    slugInput.value = tituloInput.value
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '');
  });
})();
</script>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
