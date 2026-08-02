<?php
require_once __DIR__ . '/includes/functions.php';
current_lang();
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
        $erro = t('contato.form.error_session');
    } elseif ($nome === '' || $mensagem === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = t('contato.form.error_validation');
    } else {
        $to = SITE_EMAIL;
        $subject = 'Contato pelo site: ' . ($assunto !== '' ? $assunto : 'Novo contato');
        $body = "Nome: $nome\nE-mail: $email\nAssunto: $assunto\n\nMensagem:\n$mensagem\n";
        $headers = "From: contato@marocamargo.com.br\r\nReply-To: " . $email . "\r\n";
        $enviado = @mail($to, $subject, $body, $headers);
        if ($enviado) {
            $sucesso = true;
        } else {
            $erro = t('contato.form.error_send');
        }
    }
}

$page_title = t('contato.title');
$page_description = t('contato.description');
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="padding:90px 0 70px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker"><?= e(t('contato.hero.kicker')) ?></p>
      <h1><?= e(t('contato.hero.title')) ?></h1>
      <p class="lead" style="margin:0 auto;"><?= e(t('contato.hero.lead')) ?></p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container contact-grid">
    <div>
      <p class="eyebrow"><?= e(t('contato.direct.eyebrow')) ?></p>
      <h2><?= e(t('contato.direct.title')) ?></h2>
      <ul class="contact-list">
        <li><a href="mailto:<?= e(SITE_EMAIL) ?>">✉️ <?= e(SITE_EMAIL) ?></a></li>
        <li><a href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>?text=<?= urlencode(WHATSAPP_MESSAGE) ?>" target="_blank" rel="noopener">💬 <?= e(t('contato.direct.whatsapp')) ?></a></li>
        <li><a href="<?= e(INSTAGRAM_URL) ?>" target="_blank" rel="noopener">📷 <?= e(t('contato.direct.instagram')) ?></a></li>
        <li><a href="<?= e(LINKEDIN_URL) ?>" target="_blank" rel="noopener">💼 LinkedIn</a></li>
        <li><a href="<?= e(YOUTUBE_URL) ?>" target="_blank" rel="noopener">▶️ YouTube</a></li>
        <li><a href="<?= e(FACEBOOK_URL) ?>" target="_blank" rel="noopener">👍 Facebook</a></li>
        <li><a href="<?= e(MEDIUM_URL) ?>" target="_blank" rel="noopener">✍️ Medium</a></li>
        <li><a href="<?= e(LATTES_URL) ?>" target="_blank" rel="noopener">🎓 <?= e(t('footer.lattes')) ?></a></li>
      </ul>
    </div>
    <div class="card">
      <p class="eyebrow"><?= e(t('contato.form.eyebrow')) ?></p>
      <h2 style="font-size:1.4rem;"><?= e(t('contato.form.title')) ?></h2>

      <?php if ($sucesso): ?>
        <div class="alert alert-success"><?= e(t('contato.form.success')) ?></div>
      <?php else: ?>
        <?php if ($erro): ?><div class="alert alert-error"><?= e($erro) ?></div><?php endif; ?>
        <form method="post" novalidate>
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <div style="position:absolute; left:-9999px;" aria-hidden="true">
            <label for="website">Website</label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="nome"><?= e(t('contato.form.label_name')) ?></label>
            <input class="form-control" type="text" id="nome" name="nome" required value="<?= e($_POST['nome'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="email"><?= e(t('contato.form.label_email')) ?></label>
            <input class="form-control" type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="assunto"><?= e(t('contato.form.label_subject')) ?></label>
            <input class="form-control" type="text" id="assunto" name="assunto" placeholder="<?= e(t('contato.form.placeholder_subject')) ?>" value="<?= e($_POST['assunto'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="mensagem"><?= e(t('contato.form.label_message')) ?></label>
            <textarea class="form-control" id="mensagem" name="mensagem" rows="5" required><?= e($_POST['mensagem'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn btn-accent" style="width:100%; justify-content:center;"><?= e(t('contato.form.submit')) ?></button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
