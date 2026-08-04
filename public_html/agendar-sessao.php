<?php
require_once __DIR__ . '/includes/functions.php';

$codigo = $_GET['pedido'] ?? '';
$pedido = $codigo !== '' ? buscar_pedido_por_codigo($codigo) : null;

$page_title = t('agendar_sessao.title');
require __DIR__ . '/includes/header.php';

if (!$pedido || $pedido['status'] !== 'pago') {
    ?>
    <section class="section" style="text-align:center;">
      <div class="container">
        <h1><?= e(t('pedido.nao_encontrado')) ?></h1>
        <p><a href="/loja.php" class="btn btn-teal"><?= e(t('loja.not_found.cta')) ?></a></p>
      </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$itens = buscar_itens_pedido((int) $pedido['id']);
$produtoSessao = null;
foreach ($itens as $item) {
    $produto = buscar_produto((int) $item['produto_id']);
    if ($produto && $produto['tipo'] === 'sessao') {
        $produtoSessao = $produto;
        break;
    }
}
?>

<section class="hero" style="padding:70px 0 50px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker"><?= e(t('agendar_sessao.hero.kicker')) ?></p>
      <h1><?= e(t('agendar_sessao.hero.title')) ?></h1>
      <p class="lead" style="margin:0 auto;"><?= e(t('agendar_sessao.hero.lead')) ?></p>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="container" style="max-width:640px;">
    <?php if ($pedido['agendamento_data_hora']): ?>
      <div class="alert alert-success">
        <?= e(t('agendar_sessao.ja_agendado')) ?>
        <strong><?= e(date('d/m/Y \à\s H:i', strtotime($pedido['agendamento_data_hora']))) ?></strong>
      </div>
    <?php elseif (!$produtoSessao || empty($produtoSessao['calcom_link'])): ?>
      <div class="coming-soon" style="text-align:center;">
        <span class="coming-soon__badge">⚠️</span>
        <h2><?= e(t('agendar_sessao.sem_link.title')) ?></h2>
        <p><?= e(t('agendar_sessao.sem_link.desc')) ?></p>
        <a href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>?text=<?= urlencode(t('agendar_sessao.sem_link.whatsapp_msg') . ' ' . $pedido['codigo']) ?>" target="_blank" rel="noopener" class="btn btn-teal"><?= e(t('checkout.indisponivel.cta')) ?></a>
      </div>
    <?php else: ?>
      <div class="card" style="padding:0; overflow:hidden;">
        <div id="calcom-inline" style="min-height:640px;"></div>
      </div>
      <script type="text/javascript">
      (function (C, A, L) {
        let p = function (a, ar) { a.q.push(ar); };
        let d = C.document;
        C.Cal = C.Cal || function () {
          let cal = C.Cal;
          let ar = arguments;
          if (!cal.loaded) {
            cal.ns = {};
            cal.q = cal.q || [];
            d.head.appendChild(d.createElement("script")).src = A;
            cal.loaded = true;
          }
          if (ar[0] === L) {
            const api = function () { p(api, arguments); };
            const namespace = ar[1];
            api.q = api.q || [];
            if (typeof namespace === "string") {
              cal.ns[namespace] = cal.ns[namespace] || api;
              p(cal.ns[namespace], ar);
              p(cal, ["initNamespace", namespace]);
            } else {
              p(cal, ar);
            }
            return;
          }
          p(cal, ar);
        };
      })(window, "https://app.cal.com/embed/embed.js", "init");

      Cal("init", { origin: "https://cal.com" });
      Cal("inline", {
        elementOrSelector: "#calcom-inline",
        calLink: <?= json_encode(preg_replace('~^https?://[^/]+/~', '', $produtoSessao['calcom_link'])) ?>,
        config: {
          theme: document.documentElement.getAttribute("data-theme") === "dark" ? "dark" : "light",
          name: <?= json_encode($pedido['cliente_nome']) ?>,
          email: <?= json_encode($pedido['cliente_email']) ?>,
          metadata: { codigo: <?= json_encode($pedido['codigo']) ?> },
        },
      });
      </script>
      <p class="form-hint" style="text-align:center; margin-top:16px;"><?= e(t('agendar_sessao.aviso_confirmacao')) ?></p>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
