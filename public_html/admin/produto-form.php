<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/uploads.php';
exigir_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : null);
$produto = $id ? buscar_produto($id) : null;
if ($id && !$produto) {
    header('Location: /admin/produtos.php');
    exit;
}

$erro = null;
$valores = $produto ? [
    'tipo' => $produto['tipo'],
    'nome' => $produto['nome'],
    'slug' => $produto['slug'],
    'descricao' => $produto['descricao'] ?? '',
    'preco' => number_format($produto['preco_centavos'] / 100, 2, '.', ''),
    'imagem' => $produto['imagem'],
    'permite_dedicatoria' => $produto['permite_dedicatoria'],
    'peso_gramas' => $produto['peso_gramas'] ?? '',
    'altura_cm' => $produto['altura_cm'] ?? '',
    'largura_cm' => $produto['largura_cm'] ?? '',
    'comprimento_cm' => $produto['comprimento_cm'] ?? '',
    'duracao_minutos' => $produto['duracao_minutos'] ?? '',
    'calcom_link' => $produto['calcom_link'] ?? '',
    'estoque' => $produto['estoque'] ?? '',
    'ativo' => $produto['ativo'],
] : [
    'tipo' => 'fisico', 'nome' => '', 'slug' => '', 'descricao' => '', 'preco' => '',
    'imagem' => null, 'permite_dedicatoria' => 0,
    'peso_gramas' => '', 'altura_cm' => '', 'largura_cm' => '', 'comprimento_cm' => '',
    'duracao_minutos' => '', 'calcom_link' => '', 'estoque' => '', 'ativo' => 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validar($_POST['csrf_token'] ?? null)) {
        $erro = 'Sessão expirada. Atualize a página e tente novamente.';
    } else {
        $valores = [
            'tipo' => in_array($_POST['tipo'] ?? '', ['fisico', 'sessao'], true) ? $_POST['tipo'] : 'fisico',
            'nome' => trim($_POST['nome'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
            'preco' => trim($_POST['preco'] ?? ''),
            'imagem' => $valores['imagem'],
            'permite_dedicatoria' => isset($_POST['permite_dedicatoria']) ? 1 : 0,
            'peso_gramas' => trim($_POST['peso_gramas'] ?? ''),
            'altura_cm' => trim($_POST['altura_cm'] ?? ''),
            'largura_cm' => trim($_POST['largura_cm'] ?? ''),
            'comprimento_cm' => trim($_POST['comprimento_cm'] ?? ''),
            'duracao_minutos' => trim($_POST['duracao_minutos'] ?? ''),
            'calcom_link' => trim($_POST['calcom_link'] ?? ''),
            'estoque' => trim($_POST['estoque'] ?? ''),
            'ativo' => isset($_POST['ativo']) ? 1 : 0,
        ];

        $novaImagem = null;
        if (!empty($_FILES['imagem']['name'])) {
            $novaImagem = salvar_imagem_enviada($_FILES['imagem'], 'produtos', $erroUpload);
            if ($novaImagem === null) {
                $erro = $erroUpload;
            }
        }

        if (!$erro && $valores['nome'] === '') {
            $erro = 'Nome é obrigatório.';
        } elseif (!$erro && (!is_numeric($valores['preco']) || (float) $valores['preco'] <= 0)) {
            $erro = 'Informe um preço válido, maior que zero.';
        } elseif (!$erro && $valores['calcom_link'] !== '' && !filter_var($valores['calcom_link'], FILTER_VALIDATE_URL)) {
            $erro = 'O link do Cal.com deve ser uma URL válida.';
        } elseif (!$erro && $valores['estoque'] !== '' && !ctype_digit($valores['estoque'])) {
            $erro = 'Estoque deve ser um número inteiro, ou deixe em branco para ilimitado.';
        }

        if (!$erro) {
            $slugBase = $valores['slug'] !== '' ? $valores['slug'] : $valores['nome'];
            $slugFinal = gerar_slug_unico_produto($slugBase, $id);
            $precoCentavos = (int) round(((float) $valores['preco']) * 100);
            $descricao = $valores['descricao'] !== '' ? $valores['descricao'] : null;
            $imagemFinal = $novaImagem ?? $valores['imagem'];
            $isFisico = $valores['tipo'] === 'fisico';
            $isSessao = $valores['tipo'] === 'sessao';

            $pesoGramas = $isFisico && $valores['peso_gramas'] !== '' ? (int) $valores['peso_gramas'] : null;
            $alturaCm = $isFisico && $valores['altura_cm'] !== '' ? (float) $valores['altura_cm'] : null;
            $larguraCm = $isFisico && $valores['largura_cm'] !== '' ? (float) $valores['largura_cm'] : null;
            $comprimentoCm = $isFisico && $valores['comprimento_cm'] !== '' ? (float) $valores['comprimento_cm'] : null;
            $duracaoMinutos = $isSessao && $valores['duracao_minutos'] !== '' ? (int) $valores['duracao_minutos'] : null;
            $calcomLink = $isSessao && $valores['calcom_link'] !== '' ? $valores['calcom_link'] : null;
            $estoque = $valores['estoque'] !== '' ? (int) $valores['estoque'] : null;
            $permiteDedicatoria = $isFisico ? $valores['permite_dedicatoria'] : 0;

            if ($id) {
                if ($novaImagem && $valores['imagem']) {
                    remover_imagem_enviada($valores['imagem']);
                }
                $stmt = db()->prepare('UPDATE produtos SET tipo=?, nome=?, slug=?, descricao=?, preco_centavos=?, imagem=?, permite_dedicatoria=?, peso_gramas=?, altura_cm=?, largura_cm=?, comprimento_cm=?, duracao_minutos=?, calcom_link=?, estoque=?, ativo=? WHERE id=?');
                $stmt->execute([$valores['tipo'], $valores['nome'], $slugFinal, $descricao, $precoCentavos, $imagemFinal, $permiteDedicatoria, $pesoGramas, $alturaCm, $larguraCm, $comprimentoCm, $duracaoMinutos, $calcomLink, $estoque, $valores['ativo'], $id]);
                header('Location: /admin/produtos.php?msg=atualizado');
            } else {
                $stmt = db()->prepare('INSERT INTO produtos (tipo, nome, slug, descricao, preco_centavos, imagem, permite_dedicatoria, peso_gramas, altura_cm, largura_cm, comprimento_cm, duracao_minutos, calcom_link, estoque, ativo) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
                $stmt->execute([$valores['tipo'], $valores['nome'], $slugFinal, $descricao, $precoCentavos, $imagemFinal, $permiteDedicatoria, $pesoGramas, $alturaCm, $larguraCm, $comprimentoCm, $duracaoMinutos, $calcomLink, $estoque, $valores['ativo']]);
                header('Location: /admin/produtos.php?msg=criado');
            }
            exit;
        }
    }
}

$admin_page_title = $id ? 'Editar produto' : 'Novo produto';
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <h1><?= $id ? 'Editar produto' : 'Novo produto' ?></h1>
  <a href="/admin/produtos.php" class="btn btn-ghost">← Voltar para a loja</a>
</div>

<?php if ($erro): ?><div class="alert alert-error"><?= e($erro) ?></div><?php endif; ?>

<div class="card" style="max-width:760px;">
  <form method="post" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <?php if ($id): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>

    <div class="form-group">
      <label for="tipo">Tipo de produto</label>
      <select class="form-control" id="tipo" name="tipo" style="max-width:280px;">
        <option value="fisico" <?= $valores['tipo'] === 'fisico' ? 'selected' : '' ?>>Físico (envio pelos Correios)</option>
        <option value="sessao" <?= $valores['tipo'] === 'sessao' ? 'selected' : '' ?>>Sessão (coaching, consultoria — com agendamento)</option>
      </select>
    </div>

    <div class="form-group">
      <label for="nome">Nome</label>
      <input class="form-control" type="text" id="nome" name="nome" required value="<?= e($valores['nome']) ?>">
    </div>

    <div class="form-group">
      <label for="slug">Endereço (slug)</label>
      <input class="form-control" type="text" id="slug" name="slug" value="<?= e($valores['slug']) ?>" placeholder="gerado automaticamente a partir do nome se deixado em branco">
    </div>

    <div class="form-group">
      <label for="descricao">Descrição</label>
      <textarea class="form-control" id="descricao" name="descricao" rows="4"><?= e($valores['descricao']) ?></textarea>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="preco">Preço (R$)</label>
        <input class="form-control" type="number" step="0.01" min="0.01" id="preco" name="preco" required value="<?= e($valores['preco']) ?>" placeholder="79.90">
      </div>
      <div class="form-group">
        <label for="estoque">Estoque (opcional)</label>
        <input class="form-control" type="number" min="0" id="estoque" name="estoque" value="<?= e((string) $valores['estoque']) ?>" placeholder="Deixe em branco se ilimitado">
      </div>
    </div>

    <div class="form-group">
      <label for="imagem">Imagem do produto</label>
      <?php if (!empty($valores['imagem'])): ?>
        <div style="margin-bottom:10px;"><img src="<?= e($valores['imagem']) ?>" alt="Imagem atual" style="max-width:220px; border-radius:var(--radius-sm);"></div>
      <?php endif; ?>
      <input class="form-control" type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp">
      <p class="form-hint">JPG, PNG ou WEBP, até 5MB.</p>
    </div>

    <div id="campos-fisico">
      <p class="form-hint" style="margin-bottom:14px; font-weight:600; color:var(--text-heading);">Dados para cálculo de frete</p>
      <div class="form-group" style="display:flex; align-items:center; gap:10px;">
        <input type="checkbox" id="permite_dedicatoria" name="permite_dedicatoria" value="1" <?= $valores['permite_dedicatoria'] ? 'checked' : '' ?> style="width:18px; height:18px;">
        <label for="permite_dedicatoria" style="margin:0;">Permite dedicatória/autógrafo</label>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="peso_gramas">Peso (gramas)</label>
          <input class="form-control" type="number" min="0" id="peso_gramas" name="peso_gramas" value="<?= e((string) $valores['peso_gramas']) ?>">
        </div>
        <div class="form-group">
          <label for="altura_cm">Altura (cm)</label>
          <input class="form-control" type="number" step="0.1" min="0" id="altura_cm" name="altura_cm" value="<?= e((string) $valores['altura_cm']) ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="largura_cm">Largura (cm)</label>
          <input class="form-control" type="number" step="0.1" min="0" id="largura_cm" name="largura_cm" value="<?= e((string) $valores['largura_cm']) ?>">
        </div>
        <div class="form-group">
          <label for="comprimento_cm">Comprimento (cm)</label>
          <input class="form-control" type="number" step="0.1" min="0" id="comprimento_cm" name="comprimento_cm" value="<?= e((string) $valores['comprimento_cm']) ?>">
        </div>
      </div>
    </div>

    <div id="campos-sessao">
      <p class="form-hint" style="margin-bottom:14px; font-weight:600; color:var(--text-heading);">Dados de agendamento</p>
      <div class="form-row">
        <div class="form-group">
          <label for="duracao_minutos">Duração (minutos)</label>
          <input class="form-control" type="number" min="0" id="duracao_minutos" name="duracao_minutos" value="<?= e((string) $valores['duracao_minutos']) ?>" placeholder="Ex: 60">
        </div>
        <div class="form-group">
          <label for="calcom_link">Link do tipo de evento no Cal.com</label>
          <input class="form-control" type="url" id="calcom_link" name="calcom_link" value="<?= e($valores['calcom_link']) ?>" placeholder="https://cal.com/marocamargo/coaching-60min">
        </div>
      </div>
      <p class="form-hint">Depois que o pagamento for aprovado, o cliente será direcionado para agendar o horário usando esse link. (Recurso de checkout ainda em desenvolvimento.)</p>
    </div>

    <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top:20px;">
      <input type="checkbox" id="ativo" name="ativo" value="1" <?= $valores['ativo'] ? 'checked' : '' ?> style="width:18px; height:18px;">
      <label for="ativo" style="margin:0;">Exibir na loja</label>
    </div>

    <button type="submit" class="btn btn-teal"><?= $id ? 'Salvar alterações' : 'Criar produto' ?></button>
  </form>
</div>

<script>
(function () {
  var tipoSelect = document.getElementById('tipo');
  var camposFisico = document.getElementById('campos-fisico');
  var camposSessao = document.getElementById('campos-sessao');
  function atualizar() {
    var isFisico = tipoSelect.value === 'fisico';
    camposFisico.style.display = isFisico ? '' : 'none';
    camposSessao.style.display = isFisico ? 'none' : '';
  }
  tipoSelect.addEventListener('change', atualizar);
  atualizar();

  var nomeInput = document.getElementById('nome');
  var slugInput = document.getElementById('slug');
  var slugTocado = slugInput.value !== '';
  slugInput.addEventListener('input', function () { slugTocado = true; });
  nomeInput.addEventListener('input', function () {
    if (slugTocado) return;
    slugInput.value = nomeInput.value
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '');
  });
})();
</script>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
