<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/carrinho.php';
iniciar_sessao();

if (carrinho_vazio()) {
    header('Location: /carrinho.php');
    exit;
}

$page_title = t('checkout.title');
require __DIR__ . '/includes/header.php';

$itens = carrinho_conteudo();
$subtotal = carrinho_subtotal_centavos();
$frete = FRETE_PADRAO_CENTAVOS;
$total = $subtotal + $frete;
?>

<section class="hero" style="padding:70px 0 50px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker"><?= e(t('checkout.hero.kicker')) ?></p>
      <h1><?= e(t('checkout.hero.title')) ?></h1>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="container" style="max-width:640px;">

    <div class="card" style="margin-bottom:24px;">
      <h3 style="font-size:1.05rem;"><?= e(t('checkout.resumo')) ?></h3>
      <?php foreach ($itens as $item): ?>
        <div style="display:flex; justify-content:space-between; font-size:0.92rem; padding:6px 0; color:var(--text-soft);">
          <span><?= (int) $item['quantidade'] ?>x <?= e($item['produto']['nome']) ?></span>
          <span><?= e(formatar_preco($item['subtotal_centavos'])) ?></span>
        </div>
      <?php endforeach; ?>
      <div style="display:flex; justify-content:space-between; font-size:0.92rem; padding:6px 0; color:var(--text-soft); border-top:1px solid var(--border-soft); margin-top:8px;">
        <span><?= e(t('checkout.frete')) ?></span>
        <span><?= e(formatar_preco($frete)) ?></span>
      </div>
      <div style="display:flex; justify-content:space-between; font-weight:700; font-size:1.1rem; color:var(--text-heading); padding-top:10px;">
        <span><?= e(t('checkout.total')) ?></span>
        <span><?= e(formatar_preco($total)) ?></span>
      </div>
    </div>

    <?php if (!mp_configurado()): ?>
      <div class="coming-soon" style="text-align:center;">
        <span class="coming-soon__badge"><?= e(t('common.coming_soon')) ?></span>
        <h2><?= e(t('checkout.indisponivel.title')) ?></h2>
        <p><?= e(t('checkout.indisponivel.desc')) ?></p>
        <a href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>" target="_blank" rel="noopener" class="btn btn-teal"><?= e(t('checkout.indisponivel.cta')) ?></a>
      </div>
    <?php else: ?>
      <div id="checkoutErro" class="alert alert-error" style="display:none;"></div>

      <div class="card" style="margin-bottom:24px;">
        <h3 style="font-size:1.05rem; margin-bottom:16px;"><?= e(t('checkout.dados.title')) ?></h3>
        <div class="form-group">
          <label for="cliente_nome"><?= e(t('contato.form.label_name')) ?></label>
          <input class="form-control" type="text" id="cliente_nome" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="cliente_email"><?= e(t('contato.form.label_email')) ?></label>
            <input class="form-control" type="email" id="cliente_email" required>
          </div>
          <div class="form-group">
            <label for="cliente_telefone"><?= e(t('checkout.telefone')) ?></label>
            <input class="form-control" type="tel" id="cliente_telefone">
          </div>
        </div>
        <div class="form-group">
          <label for="cliente_cpf"><?= e(t('checkout.cpf')) ?></label>
          <input class="form-control" type="text" id="cliente_cpf" placeholder="000.000.000-00" required>
        </div>
      </div>

      <div class="card" style="margin-bottom:24px;">
        <h3 style="font-size:1.05rem; margin-bottom:16px;"><?= e(t('checkout.endereco.title')) ?></h3>
        <div class="form-row">
          <div class="form-group">
            <label for="endereco_cep"><?= e(t('checkout.cep')) ?></label>
            <input class="form-control" type="text" id="endereco_cep" placeholder="00000-000" required>
          </div>
          <div class="form-group">
            <label for="endereco_numero"><?= e(t('checkout.numero')) ?></label>
            <input class="form-control" type="text" id="endereco_numero" required>
          </div>
        </div>
        <div class="form-group">
          <label for="endereco_logradouro"><?= e(t('checkout.logradouro')) ?></label>
          <input class="form-control" type="text" id="endereco_logradouro" required>
        </div>
        <div class="form-group">
          <label for="endereco_complemento"><?= e(t('checkout.complemento')) ?></label>
          <input class="form-control" type="text" id="endereco_complemento">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="endereco_bairro"><?= e(t('checkout.bairro')) ?></label>
            <input class="form-control" type="text" id="endereco_bairro" required>
          </div>
          <div class="form-group">
            <label for="endereco_cidade"><?= e(t('checkout.cidade')) ?></label>
            <input class="form-control" type="text" id="endereco_cidade" required>
          </div>
        </div>
        <div class="form-group" style="max-width:120px;">
          <label for="endereco_uf"><?= e(t('checkout.uf')) ?></label>
          <input class="form-control" type="text" id="endereco_uf" maxlength="2" required>
        </div>
      </div>

      <div class="card">
        <h3 style="font-size:1.05rem; margin-bottom:16px;"><?= e(t('checkout.pagamento.title')) ?></h3>
        <div id="paymentBrick_container"></div>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php if (mp_configurado()): ?>
<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>
(function () {
  var mp = new MercadoPago(<?= json_encode(MP_PUBLIC_KEY) ?>, { locale: 'pt-BR' });
  var erroBox = document.getElementById('checkoutErro');
  var csrfToken = <?= json_encode(csrf_token()) ?>;

  function mostrarErro(msg) {
    erroBox.textContent = msg;
    erroBox.style.display = 'block';
    erroBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function camposObrigatoriosPreenchidos() {
    var ids = ['cliente_nome', 'cliente_email', 'cliente_cpf', 'endereco_cep', 'endereco_numero', 'endereco_logradouro', 'endereco_bairro', 'endereco_cidade', 'endereco_uf'];
    for (var i = 0; i < ids.length; i++) {
      var el = document.getElementById(ids[i]);
      if (!el.value.trim()) return false;
    }
    return true;
  }

  var bricksBuilder = mp.bricks();
  bricksBuilder.create('payment', 'paymentBrick_container', {
    initialization: {
      amount: <?= json_encode($total / 100) ?>,
    },
    customization: {
      paymentMethods: {
        creditCard: 'all',
        debitCard: 'all',
        bankTransfer: ['pix'],
      },
    },
    callbacks: {
      onError: function (error) {
        console.error(error);
        mostrarErro(<?= json_encode(t('checkout.erro_pagamento')) ?>);
      },
      onSubmit: function (params) {
        var formData = params.formData;
        if (!camposObrigatoriosPreenchidos()) {
          mostrarErro(<?= json_encode(t('checkout.erro_campos')) ?>);
          return Promise.reject();
        }

        var payload = {
          csrf_token: csrfToken,
          form_data: formData,
          cliente: {
            nome: document.getElementById('cliente_nome').value.trim(),
            email: document.getElementById('cliente_email').value.trim(),
            telefone: document.getElementById('cliente_telefone').value.trim(),
            cpf: document.getElementById('cliente_cpf').value.trim(),
          },
          endereco: {
            cep: document.getElementById('endereco_cep').value.trim(),
            numero: document.getElementById('endereco_numero').value.trim(),
            logradouro: document.getElementById('endereco_logradouro').value.trim(),
            complemento: document.getElementById('endereco_complemento').value.trim(),
            bairro: document.getElementById('endereco_bairro').value.trim(),
            cidade: document.getElementById('endereco_cidade').value.trim(),
            uf: document.getElementById('endereco_uf').value.trim(),
          },
        };

        return fetch('/checkout-processar.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
          credentials: 'same-origin',
        })
          .then(function (r) { return r.json().then(function (json) { return { status: r.status, json: json }; }); })
          .then(function (res) {
            if (res.status >= 400) {
              mostrarErro(res.json.erro || <?= json_encode(t('checkout.erro_pagamento')) ?>);
              return Promise.reject();
            }
            window.location.href = res.json.redirect;
          })
          .catch(function (e) {
            if (e) console.error(e);
            mostrarErro(<?= json_encode(t('checkout.erro_pagamento')) ?>);
            return Promise.reject();
          });
      },
      onReady: function () {},
    },
  });
})();
</script>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
