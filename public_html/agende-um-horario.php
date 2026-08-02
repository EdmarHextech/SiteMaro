<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = t('agenda_horario.title');
$page_description = t('agenda_horario.description');
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="padding:90px 0 60px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker"><?= e(t('agenda_horario.hero.kicker')) ?></p>
      <h1><?= e(t('agenda_horario.hero.title')) ?></h1>
      <p class="lead" style="margin:0 auto;"><?= e(t('agenda_horario.hero.lead')) ?></p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="card" style="padding:0; overflow:hidden;">
      <div id="calcom-inline" style="min-height:640px;"></div>
    </div>
  </div>
</section>

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
  calLink: <?= json_encode(CALCOM_LINK_GERAL) ?>,
  config: {
    theme: document.documentElement.getAttribute("data-theme") === "dark" ? "dark" : "light",
  },
});
Cal("ui", {
  theme: document.documentElement.getAttribute("data-theme") === "dark" ? "dark" : "light",
  styles: { branding: { brandColor: "#146c6d" } },
  hideEventTypeDetails: false,
  layout: "month_view",
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
