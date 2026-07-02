<?php
require_once __DIR__ . '/../includes/functions.php';
iniciar_sessao();

if (admin_logado()) {
    header('Location: /admin/eventos.php');
    exit;
}

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validar($_POST['csrf_token'] ?? null)) {
        $erro = 'Sessão expirada. Tente novamente.';
    } else {
        $usuario = trim($_POST['usuario'] ?? '');
        $senha = $_POST['senha'] ?? '';

        // pequeno atraso para dificultar força bruta
        usleep(300000);

        if (hash_equals(ADMIN_USERNAME, $usuario) && password_verify($senha, ADMIN_PASSWORD_HASH)) {
            session_regenerate_id(true);
            $_SESSION['admin_logado'] = true;
            header('Location: /admin/eventos.php');
            exit;
        }
        $erro = 'Usuário ou senha inválidos.';
    }
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Painel Maro Camargo</title>
<meta name="robots" content="noindex, nofollow">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/style.css">
<script>
(function () {
  try {
    var saved = localStorage.getItem('theme');
    var theme = saved || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    if (theme === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
  } catch (e) {}
})();
</script>
</head>
<body class="admin-body">
  <div class="login-shell">
    <button type="button" class="theme-toggle" id="themeToggle" aria-label="Alternar tema claro/escuro" aria-pressed="false">
      <span class="icon-light">☀️</span><span class="icon-dark">🌙</span>
    </button>
    <div class="login-card">
      <h1>Painel da Maro</h1>
      <p class="sub">Acesso restrito para atualizar a agenda</p>

      <?php if ($erro): ?><div class="alert alert-error"><?= e($erro) ?></div><?php endif; ?>

      <form method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="form-group">
          <label for="usuario">Usuário</label>
          <input class="form-control" type="text" id="usuario" name="usuario" required autofocus>
        </div>
        <div class="form-group">
          <label for="senha">Senha</label>
          <input class="form-control" type="password" id="senha" name="senha" required>
        </div>
        <button type="submit" class="btn btn-teal" style="width:100%; justify-content:center;">Entrar</button>
      </form>
    </div>
  </div>
<script src="/assets/js/main.js"></script>
</body>
</html>
