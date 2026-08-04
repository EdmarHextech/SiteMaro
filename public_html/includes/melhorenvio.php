<?php
/**
 * Integração com a API do Melhor Envio (cálculo de frete via Correios PAC/SEDEX e outras
 * transportadoras, sem precisar de contrato direto com os Correios). cURL puro, sem SDK.
 */

class MelhorEnvioException extends RuntimeException {}

function me_base_url(): string
{
    return MELHOR_ENVIO_SANDBOX
        ? 'https://sandbox.melhorenvio.com.br'
        : 'https://melhorenvio.com.br';
}

/**
 * Calcula opções de frete para o CEP de destino a partir dos itens do carrinho (cada item
 * precisa trazer peso_gramas/altura_cm/largura_cm/comprimento_cm do produto).
 * Retorna uma lista de ['servico' => string, 'preco_centavos' => int, 'prazo_dias' => int],
 * ordenada do mais barato para o mais caro. Lança MelhorEnvioException se a API falhar.
 */
function me_calcular_frete(string $cepDestino, array $itensCarrinho): array
{
    if (!melhor_envio_configurado()) {
        throw new MelhorEnvioException('Melhor Envio não está configurado.');
    }

    $produtos = [];
    foreach ($itensCarrinho as $item) {
        $produto = $item['produto'];
        $produtos[] = [
            'id' => (string) $produto['id'],
            'width' => (float) ($produto['largura_cm'] ?? 11),
            'height' => (float) ($produto['altura_cm'] ?? 2),
            'length' => (float) ($produto['comprimento_cm'] ?? 16),
            'weight' => max(0.1, (float) ($produto['peso_gramas'] ?? 300) / 1000),
            'insurance_value' => round($produto['preco_centavos'] / 100, 2),
            'quantity' => (int) $item['quantidade'],
        ];
    }

    $corpo = [
        'from' => ['postal_code' => preg_replace('~\D~', '', MELHOR_ENVIO_CEP_ORIGEM)],
        'to' => ['postal_code' => preg_replace('~\D~', '', $cepDestino)],
        'products' => $produtos,
    ];

    $ch = curl_init(me_base_url() . '/api/v2/me/shipment/calculate');
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . MELHOR_ENVIO_TOKEN,
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: ' . SITE_NAME . ' (' . SITE_EMAIL . ')',
        ],
        CURLOPT_POSTFIELDS => json_encode($corpo),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    $resposta = curl_exec($ch);
    $erroCurl = curl_error($ch);
    $statusHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // curl_close() é no-op desde PHP 8.0 e foi descontinuado no PHP 8.5 (gera warning) — omitido de propósito.

    if ($resposta === false) {
        throw new MelhorEnvioException('Falha de conexão com o Melhor Envio: ' . $erroCurl);
    }
    if ($statusHttp >= 400) {
        throw new MelhorEnvioException('Melhor Envio recusou a requisição (HTTP ' . $statusHttp . ').');
    }

    $opcoes = json_decode($resposta, true);
    if (!is_array($opcoes)) {
        throw new MelhorEnvioException('Resposta inesperada do Melhor Envio.');
    }

    $resultado = [];
    foreach ($opcoes as $opcao) {
        if (!empty($opcao['error']) || !isset($opcao['price'])) {
            continue; // serviço indisponível para essa rota/dimensões
        }
        $resultado[] = [
            'servico' => ($opcao['company']['name'] ?? '') . ' ' . ($opcao['name'] ?? ''),
            'preco_centavos' => (int) round(((float) $opcao['price']) * 100),
            'prazo_dias' => (int) ($opcao['delivery_time'] ?? 0),
        ];
    }

    usort($resultado, fn($a, $b) => $a['preco_centavos'] <=> $b['preco_centavos']);

    return $resultado;
}
