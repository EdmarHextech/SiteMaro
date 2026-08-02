<?php
/**
 * Sanitização de HTML rico vindo do editor (TinyMCE) antes de salvar no banco.
 * O conteúdo salvo é ecoado sem htmlspecialchars() nas páginas públicas — por isso a
 * sanitização aqui, no save, é a defesa real contra XSS armazenado, não só uma formalidade.
 * Usa HTML Purifier (vendorizado em assets/vendor/htmlpurifier) com um allowlist restrito.
 */
require_once __DIR__ . '/../assets/vendor/htmlpurifier/library/HTMLPurifier.auto.php';

function sanitizar_html_rico(string $html): string
{
    $config = HTMLPurifier_Config::createDefault();
    $config->set('HTML.Allowed', 'p,br,strong,em,u,span[style],h2,h3,ul,ol,li,a[href],img[src|alt],blockquote');
    $config->set('CSS.AllowedProperties', 'color,background-color');
    $config->set('AutoFormat.RemoveEmpty', true);
    $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
    $config->set('HTML.TargetBlank', true);
    $config->set('Cache.SerializerPath', __DIR__ . '/cache/htmlpurifier');

    $purifier = new HTMLPurifier($config);
    return $purifier->purify($html);
}
