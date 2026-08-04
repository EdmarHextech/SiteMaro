<?php
/**
 * Carrinho de compras em sessão. Guarda só produto_id + quantidade + dedicatória —
 * NUNCA preço. O preço é sempre relido do banco na hora de exibir e, principalmente,
 * na hora de fechar o pedido — assim fica estruturalmente impossível confiar em um
 * preço adulterado vindo do cliente.
 *
 * Um carrinho só pode ter produtos de UM tipo por vez ("fisico" OU "sessao") — simplifica
 * bastante o checkout, que ou pede endereço+frete (físico) ou pula direto pro agendamento
 * pós-pagamento (sessão), sem precisar lidar com pedidos mistos. Produto de sessão sempre
 * entra com quantidade 1 (cada sessão comprada = um agendamento próprio).
 */

function carrinho_iniciar(): void
{
    iniciar_sessao();
    if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
}

/** Tipo dos produtos atualmente no carrinho ('fisico'|'sessao'), ou null se vazio. */
function carrinho_tipo(): ?string
{
    $itens = carrinho_conteudo();
    return $itens[0]['produto']['tipo'] ?? null;
}

function carrinho_adicionar(int $produtoId, int $quantidade, ?string $dedicatoriaTexto, ?string &$erro = null): bool
{
    carrinho_iniciar();

    $produto = buscar_produto($produtoId);
    if (!$produto || !$produto['ativo']) {
        $erro = 'Produto indisponível.';
        return false;
    }

    $tipoAtual = carrinho_tipo();
    if ($tipoAtual !== null && $tipoAtual !== $produto['tipo']) {
        $erro = 'Seu carrinho já tem um tipo diferente de produto. Finalize ou esvazie o carrinho antes de adicionar este item.';
        return false;
    }

    if ($produto['tipo'] === 'sessao') {
        // Sessão: sempre 1 por pedido (cada compra vira um agendamento próprio depois do pagamento).
        $_SESSION['carrinho'] = [$produtoId => ['quantidade' => 1, 'dedicatoria_texto' => null]];
        return true;
    }

    $quantidade = max(1, min(20, $quantidade));
    $atual = $_SESSION['carrinho'][$produtoId]['quantidade'] ?? 0;
    $_SESSION['carrinho'][$produtoId] = [
        'quantidade' => max(1, min(20, $atual + $quantidade)),
        'dedicatoria_texto' => $produto['permite_dedicatoria'] && $dedicatoriaTexto ? mb_substr(trim($dedicatoriaTexto), 0, 500) : null,
    ];
    return true;
}

function carrinho_atualizar_quantidade(int $produtoId, int $quantidade): void
{
    carrinho_iniciar();
    if (!isset($_SESSION['carrinho'][$produtoId])) {
        return;
    }
    if ($quantidade <= 0) {
        unset($_SESSION['carrinho'][$produtoId]);
        return;
    }
    $produto = buscar_produto($produtoId);
    if ($produto && $produto['tipo'] === 'sessao') {
        return; // sessão é sempre 1 — não deixa burlar via "atualizar quantidade"
    }
    $_SESSION['carrinho'][$produtoId]['quantidade'] = max(1, min(20, $quantidade));
}

function carrinho_remover(int $produtoId): void
{
    carrinho_iniciar();
    unset($_SESSION['carrinho'][$produtoId]);
}

function carrinho_esvaziar(): void
{
    carrinho_iniciar();
    $_SESSION['carrinho'] = [];
}

/**
 * Recalcula o conteúdo do carrinho a partir do banco (preço e disponibilidade atuais).
 * Produtos removidos/desativados desde que foram adicionados somem silenciosamente do carrinho.
 */
function carrinho_conteudo(): array
{
    carrinho_iniciar();
    $itens = [];
    foreach ($_SESSION['carrinho'] as $produtoId => $dados) {
        $produto = buscar_produto((int) $produtoId);
        if (!$produto || !$produto['ativo']) {
            unset($_SESSION['carrinho'][$produtoId]);
            continue;
        }
        $quantidade = (int) $dados['quantidade'];
        $itens[] = [
            'produto' => $produto,
            'quantidade' => $quantidade,
            'dedicatoria_texto' => $dados['dedicatoria_texto'] ?? null,
            'subtotal_centavos' => (int) $produto['preco_centavos'] * $quantidade,
        ];
    }
    return $itens;
}

function carrinho_subtotal_centavos(): int
{
    $total = 0;
    foreach (carrinho_conteudo() as $item) {
        $total += $item['subtotal_centavos'];
    }
    return $total;
}

function carrinho_contagem(): int
{
    $total = 0;
    foreach (carrinho_conteudo() as $item) {
        $total += $item['quantidade'];
    }
    return $total;
}

function carrinho_vazio(): bool
{
    return carrinho_contagem() === 0;
}
