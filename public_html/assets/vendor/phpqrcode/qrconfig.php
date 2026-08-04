<?php
/*
 * PHP QR Code encoder
 *
 * Config file, feel free to modify
 */
     
    // Desativado (padrão upstream é true): nosso uso é baixo volume (só os QR codes das
    // etiquetas de expedição), não vale a pena depender de uma pasta cache/ gravável dentro
    // do vendor/ — evita warnings de permissão em hospedagem compartilhada.
    define('QR_CACHEABLE', false);
    define('QR_CACHE_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);  // used when QR_CACHEABLE === true
    // Desativado (padrão upstream grava um arquivo -errors.txt dentro do próprio vendor/ a
    // cada warning/deprecation do PHP durante a geração) — não queremos a lib escrevendo
    // arquivos dentro de uma pasta versionada no git. Erros reais continuam indo pro log
    // normal do PHP (error_log), isso só desliga o log duplicado e específico da lib.
    define('QR_LOG_DIR', false);
    
    define('QR_FIND_BEST_MASK', true);                                                          // if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
    define('QR_FIND_FROM_RANDOM', false);                                                       // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
    define('QR_DEFAULT_MASK', 2);                                                               // when QR_FIND_BEST_MASK === false
                                                  
    define('QR_PNG_MAXIMUM_SIZE',  1024);                                                       // maximum allowed png image width (in pixels), tune to make sure GD and PHP can handle such big images
                                                  