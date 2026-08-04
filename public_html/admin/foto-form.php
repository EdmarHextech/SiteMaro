<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/uploads.php';
exigir_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : null);
$foto = $id ? buscar_foto_galeria($id) : null;
if ($id && !$foto) {
    header('Location: /admin/galeria.php');
    exit;
}

$erro = null;
$valores = $foto ?? [
    'evento_id' => '', 'arquivo' => null, 'legenda' => '', 'pessoas_mencionadas' => '', 'ordem' => 0, 'ativo' => 1,
];

$eventos = buscar_eventos(false, false);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validar($_POST['csrf_token'] ?? null)) {
        $erro = 'Sessão expirada. Atualize a página e tente novamente.';
    } else {
        $valores = [
            'evento_id' => trim($_POST['evento_id'] ?? ''),
            'arquivo' => $valores['arquivo'],
            'legenda' => trim($_POST['legenda'] ?? ''),
            'pessoas_mencionadas' => trim($_POST['pessoas_mencionadas'] ?? ''),
            'ordem' => trim($_POST['ordem'] ?? '0'),
            'ativo' => isset($_POST['ativo']) ? 1 : 0,
        ];

        $novoArquivo = null;
        if (!empty($_FILES['arquivo']['name'])) {
            $novoArquivo = salvar_imagem_enviada($_FILES['arquivo'], 'galeria', $erroUpload);
            if ($novoArquivo === null) {
                $erro = $erroUpload;
            }
        } elseif (!$id) {
            $erro = 'Selecione uma foto para enviar.';
        }

        if (!$erro && $valores['ordem'] !== '' && !ctype_digit($valores['ordem'])) {
            $erro = 'Ordem deve ser um número inteiro.';
        }

        if (!$erro) {
            $eventoId = $valores['evento_id'] !== '' ? (int) $valores['evento_id'] : null;
            $legenda = $valores['legenda'] !== '' ? $valores['legenda'] : null;
            $pessoas = $valores['pessoas_mencionadas'] !== '' ? $valores['pessoas_mencionadas'] : null;
            $ordem = $valores['ordem'] !== '' ? (int) $valores['ordem'] : 0;
            $arquivoFinal = $novoArquivo ?? $valores['arquivo'];

            if ($id) {
                if ($novoArquivo && $valores['arquivo']) {
                    remover_imagem_enviada($valores['arquivo']);
                }
                $stmt = db()->prepare('UPDATE galeria_fotos SET evento_id=?, arquivo=?, legenda=?, pessoas_mencionadas=?, ordem=?, ativo=? WHERE id=?');
                $stmt->execute([$eventoId, $arquivoFinal, $legenda, $pessoas, $ordem, $valores['ativo'], $id]);
                header('Location: /admin/galeria.php?msg=atualizado');
            } else {
                $stmt = db()->prepare('INSERT INTO galeria_fotos (evento_id, arquivo, legenda, pessoas_mencionadas, ordem, ativo) VALUES (?,?,?,?,?,?)');
                $stmt->execute([$eventoId, $arquivoFinal, $legenda, $pessoas, $ordem, $valores['ativo']]);
                header('Location: /admin/galeria.php?msg=criado');
            }
            exit;
        }
    }
}

$admin_page_title = $id ? 'Editar foto' : 'Nova foto';
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <h1><?= $id ? 'Editar foto' : 'Nova foto' ?></h1>
  <a href="/admin/galeria.php" class="btn btn-ghost">← Voltar para a galeria</a>
</div>

<?php if ($erro): ?><div class="alert alert-error"><?= e($erro) ?></div><?php endif; ?>

<div class="card" style="max-width:640px;">
  <form method="post" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <?php if ($id): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>

    <div class="form-group">
      <label for="arquivo">Foto <?= $id ? '(deixe em branco para manter a atual)' : '' ?></label>
      <?php if (!empty($valores['arquivo'])): ?>
        <div style="margin-bottom:10px;"><img src="<?= e($valores['arquivo']) ?>" alt="Foto atual" style="max-width:260px; border-radius:var(--radius-sm);"></div>
      <?php endif; ?>
      <input class="form-control" type="file" id="arquivo" name="arquivo" accept="image/jpeg,image/png,image/webp">
      <p class="form-hint">JPG, PNG ou WEBP, até 5MB.</p>
    </div>

    <div class="form-group">
      <label for="evento_id">Evento relacionado (opcional)</label>
      <select class="form-control" id="evento_id" name="evento_id">
        <option value="">— Sem evento —</option>
        <?php foreach ($eventos as $evento): ?>
          <option value="<?= (int) $evento['id'] ?>" <?= (string) $valores['evento_id'] === (string) $evento['id'] ? 'selected' : '' ?>>
            <?= e($evento['titulo']) ?> (<?= e(date('d/m/Y', strtotime($evento['data_evento']))) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="legenda">Legenda (opcional)</label>
      <input class="form-control" type="text" id="legenda" name="legenda" value="<?= e($valores['legenda']) ?>">
    </div>

    <div class="form-group">
      <label for="pessoas_mencionadas">Pessoas na foto (opcional)</label>
      <input class="form-control" type="text" id="pessoas_mencionadas" name="pessoas_mencionadas" value="<?= e($valores['pessoas_mencionadas']) ?>" placeholder="Ex: João Silva, Maria Souza">
      <p class="form-hint">Separe os nomes por vírgula.</p>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="ordem">Ordem de exibição</label>
        <input class="form-control" type="number" min="0" id="ordem" name="ordem" value="<?= e((string) $valores['ordem']) ?>">
      </div>
      <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top:26px;">
        <input type="checkbox" id="ativo" name="ativo" value="1" <?= $valores['ativo'] ? 'checked' : '' ?> style="width:18px; height:18px;">
        <label for="ativo" style="margin:0;">Exibir no site</label>
      </div>
    </div>

    <button type="submit" class="btn btn-teal"><?= $id ? 'Salvar alterações' : 'Adicionar foto' ?></button>
  </form>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
