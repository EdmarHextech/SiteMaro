<?php
require_once __DIR__ . '/../includes/functions.php';
exigir_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : null);
$evento = $id ? buscar_evento($id) : null;
if ($id && !$evento) {
    header('Location: /admin/eventos.php');
    exit;
}

$erro = null;
$valores = $evento ?? [
    'titulo' => '', 'tipo' => 'palestra', 'local' => '', 'link_inscricao' => '', 'video_youtube_url' => '',
    'data_evento' => '', 'hora_evento' => '', 'vagas' => '', 'descricao' => '', 'ativo' => 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validar($_POST['csrf_token'] ?? null)) {
        $erro = 'Sessão expirada. Atualize a página e tente novamente.';
    } else {
        $valores = [
            'titulo' => trim($_POST['titulo'] ?? ''),
            'tipo' => in_array($_POST['tipo'] ?? '', ['palestra', 'consultoria', 'workshop', 'outro'], true) ? $_POST['tipo'] : 'palestra',
            'local' => trim($_POST['local'] ?? ''),
            'link_inscricao' => trim($_POST['link_inscricao'] ?? ''),
            'video_youtube_url' => trim($_POST['video_youtube_url'] ?? ''),
            'data_evento' => trim($_POST['data_evento'] ?? ''),
            'hora_evento' => trim($_POST['hora_evento'] ?? ''),
            'vagas' => trim($_POST['vagas'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
            'ativo' => isset($_POST['ativo']) ? 1 : 0,
        ];

        if ($valores['titulo'] === '' || $valores['local'] === '') {
            $erro = 'Título e local são obrigatórios.';
        } elseif (!DateTime::createFromFormat('Y-m-d', $valores['data_evento'])) {
            $erro = 'Informe uma data válida.';
        } elseif (!DateTime::createFromFormat('H:i', $valores['hora_evento']) && !DateTime::createFromFormat('H:i:s', $valores['hora_evento'])) {
            $erro = 'Informe um horário válido.';
        } elseif ($valores['link_inscricao'] !== '' && !filter_var($valores['link_inscricao'], FILTER_VALIDATE_URL)) {
            $erro = 'O link de inscrição deve ser uma URL válida (começando com https://).';
        } elseif ($valores['video_youtube_url'] !== '' && (!filter_var($valores['video_youtube_url'], FILTER_VALIDATE_URL) || !extrair_youtube_id($valores['video_youtube_url']))) {
            $erro = 'O link do vídeo deve ser uma URL válida do YouTube (youtube.com/watch?v=... ou youtu.be/...).';
        } elseif ($valores['vagas'] !== '' && (!ctype_digit($valores['vagas']) || (int) $valores['vagas'] < 0)) {
            $erro = 'Vagas deve ser um número inteiro positivo, ou deixe em branco.';
        } else {
            $vagas = $valores['vagas'] === '' ? null : (int) $valores['vagas'];
            $link = $valores['link_inscricao'] === '' ? null : $valores['link_inscricao'];
            $video = $valores['video_youtube_url'] === '' ? null : $valores['video_youtube_url'];
            $descricao = $valores['descricao'] === '' ? null : $valores['descricao'];

            if ($id) {
                $stmt = db()->prepare('UPDATE eventos SET titulo=?, tipo=?, local=?, link_inscricao=?, video_youtube_url=?, data_evento=?, hora_evento=?, vagas=?, descricao=?, ativo=? WHERE id=?');
                $stmt->execute([$valores['titulo'], $valores['tipo'], $valores['local'], $link, $video, $valores['data_evento'], $valores['hora_evento'], $vagas, $descricao, $valores['ativo'], $id]);
                header('Location: /admin/eventos.php?msg=atualizado');
            } else {
                $stmt = db()->prepare('INSERT INTO eventos (titulo, tipo, local, link_inscricao, video_youtube_url, data_evento, hora_evento, vagas, descricao, ativo) VALUES (?,?,?,?,?,?,?,?,?,?)');
                $stmt->execute([$valores['titulo'], $valores['tipo'], $valores['local'], $link, $video, $valores['data_evento'], $valores['hora_evento'], $vagas, $descricao, $valores['ativo']]);
                header('Location: /admin/eventos.php?msg=criado');
            }
            exit;
        }
    }
}

$admin_page_title = $id ? 'Editar evento' : 'Novo evento';
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <h1><?= $id ? 'Editar evento' : 'Novo evento' ?></h1>
  <a href="/admin/eventos.php" class="btn btn-ghost">← Voltar para a agenda</a>
</div>

<?php if ($erro): ?><div class="alert alert-error"><?= e($erro) ?></div><?php endif; ?>

<div class="card" style="max-width:720px;">
  <form method="post" novalidate>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <?php if ($id): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>

    <div class="form-group">
      <label for="titulo">Título do evento</label>
      <input class="form-control" type="text" id="titulo" name="titulo" required value="<?= e($valores['titulo']) ?>" placeholder="Ex: Palestra sobre Diálogo e World Café">
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="tipo">Tipo</label>
        <select class="form-control" id="tipo" name="tipo">
          <?php foreach (['palestra' => 'Palestra', 'consultoria' => 'Consultoria', 'workshop' => 'Workshop', 'outro' => 'Outro'] as $val => $label): ?>
            <option value="<?= e($val) ?>" <?= $valores['tipo'] === $val ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="local">Local</label>
        <input class="form-control" type="text" id="local" name="local" required value="<?= e($valores['local']) ?>" placeholder="Ex: São Paulo, SP ou Online">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="data_evento">Data</label>
        <input class="form-control" type="date" id="data_evento" name="data_evento" required value="<?= e($valores['data_evento']) ?>">
      </div>
      <div class="form-group">
        <label for="hora_evento">Horário</label>
        <input class="form-control" type="time" id="hora_evento" name="hora_evento" required value="<?= e(substr($valores['hora_evento'] ?? '', 0, 5)) ?>">
      </div>
    </div>
    <p class="form-hint" style="margin-top:-12px; margin-bottom:20px;">O dia da semana é calculado automaticamente a partir da data.</p>

    <div class="form-row">
      <div class="form-group">
        <label for="link_inscricao">Link de inscrição (opcional)</label>
        <input class="form-control" type="url" id="link_inscricao" name="link_inscricao" value="<?= e($valores['link_inscricao']) ?>" placeholder="https://...">
      </div>
      <div class="form-group">
        <label for="vagas">Vagas (opcional)</label>
        <input class="form-control" type="number" min="0" id="vagas" name="vagas" value="<?= e((string) $valores['vagas']) ?>" placeholder="Deixe em branco se ilimitado">
      </div>
    </div>

    <div class="form-group">
      <label for="video_youtube_url">Vídeo-convite do YouTube (opcional)</label>
      <input class="form-control" type="url" id="video_youtube_url" name="video_youtube_url" value="<?= e($valores['video_youtube_url']) ?>" placeholder="https://youtu.be/...">
      <p class="form-hint">Para adicionar um vídeo: faça upload no <strong>YouTube Studio</strong>, em "Visibilidade" escolha <strong>Não listado</strong> (assim ele não aparece em buscas nem no seu canal — só quem tem o link consegue assistir), depois copie o link de compartilhamento e cole aqui.</p>
    </div>

    <div class="form-group">
      <label for="descricao">Descrição curta (opcional)</label>
      <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= e($valores['descricao']) ?></textarea>
    </div>

    <div class="form-group" style="display:flex; align-items:center; gap:10px;">
      <input type="checkbox" id="ativo" name="ativo" value="1" <?= $valores['ativo'] ? 'checked' : '' ?> style="width:18px; height:18px;">
      <label for="ativo" style="margin:0;">Exibir no site</label>
    </div>

    <button type="submit" class="btn btn-teal"><?= $id ? 'Salvar alterações' : 'Criar evento' ?></button>
  </form>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
