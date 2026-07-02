<?php
const IDIOMAS_DISPONIVEIS = ['pt', 'en', 'es'];
const IDIOMA_PADRAO = 'pt';

function current_lang(): string
{
    static $lang = null;
    if ($lang !== null) {
        return $lang;
    }

    $solicitado = $_GET['lang'] ?? null;
    if (is_string($solicitado) && in_array($solicitado, IDIOMAS_DISPONIVEIS, true)) {
        $lang = $solicitado;
        if (!headers_sent()) {
            setcookie('site_lang', $lang, time() + 60 * 60 * 24 * 365, '/');
        }
        return $lang;
    }

    $cookie = $_COOKIE['site_lang'] ?? null;
    if (is_string($cookie) && in_array($cookie, IDIOMAS_DISPONIVEIS, true)) {
        $lang = $cookie;
        return $lang;
    }

    $lang = IDIOMA_PADRAO;
    return $lang;
}

function html_lang_attr(): string
{
    return ['pt' => 'pt-BR', 'en' => 'en', 'es' => 'es'][current_lang()] ?? 'pt-BR';
}

function carregar_traducoes(string $lang): array
{
    static $cache = [];
    if (isset($cache[$lang])) {
        return $cache[$lang];
    }
    $arquivo = __DIR__ . "/lang/{$lang}.php";
    $cache[$lang] = file_exists($arquivo) ? require $arquivo : [];
    return $cache[$lang];
}

function t(string $chave): string
{
    $lang = current_lang();
    $strings = carregar_traducoes($lang);
    if (isset($strings[$chave])) {
        return $strings[$chave];
    }
    $fallback = carregar_traducoes(IDIOMA_PADRAO);
    return $fallback[$chave] ?? $chave;
}

/** Link para trocar de idioma preservando a página atual. */
function link_idioma(string $lang): string
{
    $script = basename($_SERVER['SCRIPT_NAME']);
    return '/' . $script . '?lang=' . $lang;
}
