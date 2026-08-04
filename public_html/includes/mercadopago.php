<?php
/**
 * Integração com a API REST do Mercado Pago via cURL puro — sem SDK server-side, sem Composer,
 * consistente com o restante do projeto. A tokenização do cartão acontece no navegador via
 * SDK JS oficial (sdk.mercadopago.com/js/v2), então o número do cartão nunca passa por aqui.
 */

class MercadoPagoException extends RuntimeException {}

function mp_request(string $metodo, string $endpoint, ?array $corpo = null, ?string $idempotencyKey = null): array
{
    if (!mp_configurado()) {
        throw new MercadoPagoException('Mercado Pago não está configurado (MP_ACCESS_TOKEN ausente).');
    }

    $headers = [
        'Authorization: Bearer ' . MP_ACCESS_TOKEN,
        'Content-Type: application/json',
    ];
    if ($idempotencyKey !== null) {
        $headers[] = 'X-Idempotency-Key: ' . $idempotencyKey;
    }

    $ch = curl_init('https://api.mercadopago.com' . $endpoint);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $metodo,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_POSTFIELDS => $corpo !== null ? json_encode($corpo) : null,
    ]);
    $resposta = curl_exec($ch);
    $erroCurl = curl_error($ch);
    $statusHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // curl_close() é no-op desde PHP 8.0 e foi descontinuado no PHP 8.5 (gera warning) — omitido de propósito.

    if ($resposta === false) {
        throw new MercadoPagoException('Falha de conexão com o Mercado Pago: ' . $erroCurl);
    }

    $json = json_decode($resposta, true);
    if ($statusHttp >= 400) {
        $mensagem = $json['message'] ?? ('Erro HTTP ' . $statusHttp);
        throw new MercadoPagoException('Mercado Pago recusou a requisição: ' . $mensagem);
    }

    return $json ?? [];
}

/**
 * Cria um pagamento (cartão ou Pix). $dados já deve trazer o valor final calculado no servidor
 * (nunca repassar um total vindo do cliente) e, se cartão, o token gerado no navegador.
 */
function mp_criar_pagamento(array $dados, string $idempotencyKey): array
{
    return mp_request('POST', '/v1/payments', $dados, $idempotencyKey);
}

function mp_consultar_pagamento(string $paymentId): array
{
    return mp_request('GET', '/v1/payments/' . rawurlencode($paymentId));
}

/**
 * Valida a assinatura do webhook do Mercado Pago (header x-signature) conforme o esquema
 * documentado: HMAC-SHA256 sobre "id:{dataId};request-id:{requestId};ts:{ts};" usando
 * MP_WEBHOOK_SECRET. Nunca processar um webhook sem essa validação passar.
 */
function mp_validar_assinatura_webhook(string $xSignature, string $xRequestId, string $dataId): bool
{
    if (MP_WEBHOOK_SECRET === '') {
        return false;
    }

    $partes = [];
    foreach (explode(',', $xSignature) as $par) {
        [$chave, $valor] = array_pad(explode('=', trim($par), 2), 2, null);
        if ($chave !== null && $valor !== null) {
            $partes[$chave] = $valor;
        }
    }
    $ts = $partes['ts'] ?? null;
    $v1 = $partes['v1'] ?? null;
    if ($ts === null || $v1 === null) {
        return false;
    }

    $manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$ts};";
    $assinaturaCalculada = hash_hmac('sha256', $manifest, MP_WEBHOOK_SECRET);

    return hash_equals($assinaturaCalculada, $v1);
}
