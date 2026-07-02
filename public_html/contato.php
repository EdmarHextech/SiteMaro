<?php
require_once __DIR__ . '/includes/functions.php';
iniciar_sessao();

$sucesso = false;
$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $assunto = trim($_POST['assunto'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');
    $honeypot = trim($_POST['website'] ?? ''); // campo invisível, deve ficar vazio

    if ($honeypot !== '') {
        // provável spam: finge sucesso sem enviar
        $sucesso = true;
    } elseif (!csrf_validar($_POST['csrf_token'] ?? null)) {
        $erro = 'Sessão expirada. Atualize a página e tente novamente.';
    } elseif ($nome === '' || $mensagem === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Preencha nome, e-mail válido e mensagem.';
    } else {
        $to = SITE_EMAIL;
        $subject = 'Contato pelo site: ' . ($assunto !== '' ? $assunto : 'Novo contato');
        $body = "Nome: $nome\nE-mail: $email\nAssunto: $assunto\n\nMensagem:\n$mensagem\n";
        $headers = "From: contato@marocamargo.com.br\r\nReply-To: " . $email . "\r\n";
        $enviado = @mail($to, $subject, $body, $headers);
        if ($enviado) {
            $sucesso = true;
        } else {
            $erro = 'Não foi possível enviar sua mensagem agora. Tente novamente ou use o e-mail/Instagram abaixo.';
        }
    }
}

$page_title = 'Contato — Maro Camargo';
$page_description = 'Fale com Maro Camargo sobre palestras, consultorias, o livro Ponto de Encontro ou parcerias.';
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="padding:90px 0 70px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker">Contato</p>
      <h1>Vamos conversar?</h1>
      <p class="lead" style="margin:0 auto;">Palestras, consultorias, o livro Ponto de Encontro ou parcerias — escolha o canal que preferir.</p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container contact-grid">
    <div>
      <p class="eyebrow">Canais diretos</p>
      <h2>Fale diretamente com a Maro</h2>
      <ul class="contact-list">
        <li><a href="mailto:<?= e(SITE_EMAIL) ?>">✉️ <?= e(SITE_EMAIL) ?></a></li>
        <li><a href="<?= e(INSTAGRAM_URL) ?>" target="_blank" rel="noopener">📷 Instagram — @camargomaro</a></li>
        <li><a href="<?= e(LINKEDIN_URL) ?>" target="_blank" rel="noopener">💼 LinkedIn</a></li>
        <li><a href="<?= e(LATTES_URL) ?>" target="_blank" rel="noopener">🎓 Currículo Lattes</a></li>
      </ul>
    </div>
    <div class="card">
      <p class="eyebrow">Envie uma mensagem</p>
      <h2 style="font-size:1.4rem;">Formulário de contato</h2>

      <?php if ($sucesso): ?>
        <div class="alert alert-success">Mensagem enviada com sucesso! A Maro vai retornar em breve.</div>
      <?php else: ?>
        <?php if ($erro): ?><div class="alert alert-error"><?= e($erro) ?></div><?php endif; ?>
        <form method="post" novalidate>
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <div style="position:absolute; left:-9999px;" aria-hidden="true">
            <label for="website">Website</label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="nome">Nome</label>
            <input class="form-control" type="text" id="nome" name="nome" required value="<?= e($_POST['nome'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="email">E-mail</label>
            <input class="form-control" type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="assunto">Assunto</label>
            <input class="form-control" type="text" id="assunto" name="assunto" placeholder="Palestra, consultoria, livro..." value="<?= e($_POST['assunto'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="mensagem">Mensagem</label>
            <textarea class="form-control" id="mensagem" name="mensagem" rows="5" required><?= e($_POST['mensagem'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn btn-accent" style="width:100%; justify-content:center;">Enviar mensagem</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
