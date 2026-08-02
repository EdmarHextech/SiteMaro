<?php
/**
 * Helper compartilhado de upload de imagens (capa de post, fotos de galeria, imagem de produto).
 * Nunca confia em nome de arquivo ou content-type enviado pelo navegador — valida MIME real,
 * revalida como imagem de verdade, recodifica via GD (remove EXIF) e salva com nome aleatório.
 */

const UPLOAD_TAMANHO_MAX_BYTES = 5 * 1024 * 1024; // 5MB
const UPLOAD_DIMENSAO_MAX_PX = 4000;

/**
 * Valida e salva um arquivo de imagem enviado (um item de $_FILES) em assets/uploads/{$subpasta}/.
 * Retorna o caminho web (ex: "/assets/uploads/blog/ab12cd34.jpg") em caso de sucesso, ou null em caso
 * de erro — nesse caso $erro é preenchido com uma mensagem amigável para exibir no formulário.
 */
function salvar_imagem_enviada(array $arquivo, string $subpasta, ?string &$erro = null): ?string
{
    if (!isset($arquivo['error']) || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        $erro = 'Nenhum arquivo enviado.';
        return null;
    }
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        $erro = 'Falha ao enviar o arquivo. Tente novamente.';
        return null;
    }
    if (!is_uploaded_file($arquivo['tmp_name'])) {
        $erro = 'Upload inválido.';
        return null;
    }
    if ($arquivo['size'] > UPLOAD_TAMANHO_MAX_BYTES) {
        $erro = 'A imagem deve ter no máximo 5MB.';
        return null;
    }

    // MIME real do conteúdo do arquivo — nunca confiar em $arquivo['type'] (vem do navegador).
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $arquivo['tmp_name']);
    finfo_close($finfo);

    $extensoesPorMime = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    if (!isset($extensoesPorMime[$mime])) {
        $erro = 'Formato de imagem não suportado. Envie JPG, PNG ou WEBP.';
        return null;
    }

    // Revalida como imagem de verdade (getimagesize falha em muitos polyglots que enganam o finfo).
    $info = @getimagesize($arquivo['tmp_name']);
    if ($info === false) {
        $erro = 'O arquivo enviado não é uma imagem válida.';
        return null;
    }
    [$largura, $altura] = $info;
    if ($largura > UPLOAD_DIMENSAO_MAX_PX || $altura > UPLOAD_DIMENSAO_MAX_PX) {
        $erro = 'A imagem excede as dimensões máximas permitidas (' . UPLOAD_DIMENSAO_MAX_PX . 'px).';
        return null;
    }

    // Recodifica via GD: remove EXIF/metadados e neutraliza qualquer payload escondido no arquivo original.
    $imagem = match ($mime) {
        'image/jpeg' => imagecreatefromjpeg($arquivo['tmp_name']),
        'image/png' => imagecreatefrompng($arquivo['tmp_name']),
        'image/webp' => imagecreatefromwebp($arquivo['tmp_name']),
        default => false,
    };
    if ($imagem === false) {
        $erro = 'Não foi possível processar a imagem enviada.';
        return null;
    }

    $pastaDestino = __DIR__ . '/../assets/uploads/' . $subpasta;
    if (!is_dir($pastaDestino) && !mkdir($pastaDestino, 0755, true) && !is_dir($pastaDestino)) {
        $erro = 'Não foi possível preparar a pasta de destino.';
        return null;
    }

    $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $extensoesPorMime[$mime];
    $caminhoCompleto = $pastaDestino . '/' . $nomeArquivo;

    $sucesso = match ($mime) {
        'image/jpeg' => imagejpeg($imagem, $caminhoCompleto, 88),
        'image/png' => imagepng($imagem, $caminhoCompleto, 6),
        'image/webp' => imagewebp($imagem, $caminhoCompleto, 88),
        default => false,
    };
    imagedestroy($imagem);

    if (!$sucesso) {
        $erro = 'Não foi possível salvar a imagem.';
        return null;
    }

    return '/assets/uploads/' . $subpasta . '/' . $nomeArquivo;
}

/** Remove um arquivo de imagem previamente salvo por salvar_imagem_enviada(), a partir do caminho web salvo no banco. */
function remover_imagem_enviada(?string $caminhoWeb): void
{
    if (!$caminhoWeb || !str_starts_with($caminhoWeb, '/assets/uploads/')) {
        return;
    }
    $caminhoCompleto = __DIR__ . '/..' . $caminhoWeb;
    if (is_file($caminhoCompleto)) {
        @unlink($caminhoCompleto);
    }
}
