/**
 * Calcula frete em tempo real no checkout: ao preencher o CEP, busca opções no
 * Melhor Envio (via frete-calcular.php) e deixa o cliente escolher o serviço.
 * O preço exibido aqui é só para conferência do cliente — o valor realmente cobrado
 * é sempre recalculado no servidor em checkout-processar.php.
 */
document.addEventListener('DOMContentLoaded', function () {
  var cepInput = document.getElementById('endereco_cep');
  var container = document.getElementById('freteOpcoes');
  if (!cepInput || !container) return;

  function formatarPreco(centavos) {
    return 'R$ ' + (centavos / 100).toFixed(2).replace('.', ',');
  }

  function renderOpcoes(dados) {
    container.innerHTML = '';
    var opcoes = dados.opcoes || [];

    if (dados.fallback || opcoes.length === 0) {
      opcoes = [{ servico: 'Frete padrão', preco_centavos: dados.frete_padrao_centavos, prazo_dias: null }];
    }

    opcoes.forEach(function (opcao, i) {
      var id = 'frete-opcao-' + i;
      var label = document.createElement('label');
      label.className = 'frete-opcao';
      label.setAttribute('for', id);

      var input = document.createElement('input');
      input.type = 'radio';
      input.name = 'frete_opcao';
      input.id = id;
      input.value = opcao.servico;
      input.dataset.preco = opcao.preco_centavos;
      if (i === 0) input.checked = true;
      input.addEventListener('change', atualizarSelecao);

      var texto = document.createElement('span');
      texto.textContent = opcao.servico + ' — ' + formatarPreco(opcao.preco_centavos) +
        (opcao.prazo_dias ? ' (até ' + opcao.prazo_dias + ' dias úteis)' : '');

      label.appendChild(input);
      label.appendChild(texto);
      container.appendChild(label);
    });

    atualizarSelecao();
  }

  function atualizarSelecao() {
    var selecionado = container.querySelector('input[name="frete_opcao"]:checked');
    var evento = new CustomEvent('frete:selecionado', {
      detail: selecionado ? { servico: selecionado.value, precoCentavos: parseInt(selecionado.dataset.preco, 10) } : null,
    });
    document.dispatchEvent(evento);
  }

  function buscarFrete() {
    var cep = cepInput.value.replace(/\D/g, '');
    if (cep.length !== 8) return;
    container.innerHTML = '<p class="form-hint">Calculando frete…</p>';
    fetch('/frete-calcular.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ cep: cep }),
      credentials: 'same-origin',
    })
      .then(function (r) { return r.json(); })
      .then(renderOpcoes)
      .catch(function () {
        container.innerHTML = '<p class="form-hint">Não foi possível calcular o frete agora. Ele será confirmado no pagamento.</p>';
      });
  }

  cepInput.addEventListener('blur', buscarFrete);
});
