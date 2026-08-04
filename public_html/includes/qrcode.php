<?php
/** Gera um QR code PNG e retorna como data URI base64, pronto para um <img src="...">. */
require_once __DIR__ . '/../assets/vendor/phpqrcode/qrlib.php';

function gerar_qrcode_data_uri(string $texto, int $pixelPerPoint = 4, int $margem = 2): string
{
    $arquivoTemp = tempnam(sys_get_temp_dir(), 'qr_');
    QRcode::png($texto, $arquivoTemp, QR_ECLEVEL_M, $pixelPerPoint, $margem);
    $conteudo = file_get_contents($arquivoTemp);
    unlink($arquivoTemp);
    return 'data:image/png;base64,' . base64_encode($conteudo);
}
