<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/i18n.php';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Retorna as partes formatadas de uma data de evento para exibição em destaque, no idioma atual. */
function formatar_data_evento(string $data, string $hora): array
{
    $ts = strtotime($data . ' ' . $hora);
    $strings = carregar_traducoes(current_lang());
    return [
        'dia' => date('d', $ts),
        'mes' => $strings['meses'][(int) date('n', $ts)],
        'ano' => date('Y', $ts),
        'dia_semana' => $strings['dias_semana'][(int) date('w', $ts)],
        'hora' => substr($hora, 0, 5),
    ];
}

const SLUG_MAPA_ACENTOS = [
    'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
    'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
    'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
    'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
    'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
    'ç' => 'c', 'ñ' => 'n', 'ý' => 'y',
    'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
    'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
    'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
    'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
    'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
    'Ç' => 'C', 'Ñ' => 'N', 'Ý' => 'Y',
];

/**
 * Gera um slug (URL amigável) a partir de um texto livre, ex: "Título Ótimo!" -> "titulo-otimo".
 * Usa um mapa de acentos manual em vez de iconv//TRANSLIT: esse comportamento varia entre
 * plataformas (testado: macOS produz "sess~ao" para "Sessão" onde Linux produziria "sessao"),
 * o que geraria slugs diferentes em dev e no servidor de produção.
 */
function gerar_slug(string $texto): string
{
    $transliterado = strtr($texto, SLUG_MAPA_ACENTOS);
    $slug = strtolower($transliterado);
    $slug = preg_replace('~[^a-z0-9]+~', '-', $slug);
    $slug = trim($slug, '-');
    return $slug !== '' ? $slug : 'item';
}

/** Extrai o ID de um vídeo a partir de uma URL do youtube.com ou youtu.be. Retorna null se não reconhecer o formato. */
function extrair_youtube_id(?string $url): ?string
{
    if (!$url) {
        return null;
    }
    if (preg_match('~youtu\.be/([A-Za-z0-9_-]{11})~', $url, $m)) {
        return $m[1];
    }
    if (preg_match('~[?&]v=([A-Za-z0-9_-]{11})~', $url, $m)) {
        return $m[1];
    }
    if (preg_match('~youtube(?:-nocookie)?\.com/embed/([A-Za-z0-9_-]{11})~', $url, $m)) {
        return $m[1];
    }
    return null;
}

function buscar_eventos(bool $apenas_futuros = true, bool $apenas_ativos = true): array
{
    $sql = 'SELECT * FROM eventos WHERE 1=1';
    if ($apenas_ativos) {
        $sql .= ' AND ativo = 1';
    }
    if ($apenas_futuros) {
        $sql .= ' AND data_evento >= CURDATE()';
    }
    $sql .= ' ORDER BY data_evento ASC, hora_evento ASC';
    return db()->query($sql)->fetchAll();
}

function buscar_evento(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM eventos WHERE id = ?');
    $stmt->execute([$id]);
    $evento = $stmt->fetch();
    return $evento ?: null;
}

// ---------- Blog ----------
function buscar_posts_publicados(int $pagina = 1, int $porPagina = 9): array
{
    $offset = max(0, ($pagina - 1) * $porPagina);
    $stmt = db()->prepare("SELECT * FROM posts WHERE status = 'publicado' ORDER BY publicado_em DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $porPagina, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function contar_posts_publicados(): int
{
    return (int) db()->query("SELECT COUNT(*) FROM posts WHERE status = 'publicado'")->fetchColumn();
}

function buscar_post_por_slug(string $slug): ?array
{
    $stmt = db()->prepare("SELECT * FROM posts WHERE slug = ? AND status = 'publicado'");
    $stmt->execute([$slug]);
    $post = $stmt->fetch();
    return $post ?: null;
}

function buscar_post(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    return $post ?: null;
}

function post_slug_em_uso(string $slug, ?int $ignorarId = null): bool
{
    if ($ignorarId) {
        $stmt = db()->prepare('SELECT COUNT(*) FROM posts WHERE slug = ? AND id != ?');
        $stmt->execute([$slug, $ignorarId]);
    } else {
        $stmt = db()->prepare('SELECT COUNT(*) FROM posts WHERE slug = ?');
        $stmt->execute([$slug]);
    }
    return (int) $stmt->fetchColumn() > 0;
}

/** Gera um slug único para posts, adicionando sufixo -2, -3... em caso de colisão. */
function gerar_slug_unico_post(string $textoBase, ?int $ignorarId = null): string
{
    $base = gerar_slug($textoBase);
    $slug = $base;
    $i = 2;
    while (post_slug_em_uso($slug, $ignorarId)) {
        $slug = $base . '-' . $i;
        $i++;
    }
    return $slug;
}

// ---------- Galeria ----------
/** Fotos ativas, eventos primeiro (mais recentes), fotos sem evento por último. */
function buscar_fotos_galeria(): array
{
    $sql = "SELECT gf.*, e.titulo AS evento_titulo, e.data_evento AS evento_data
            FROM galeria_fotos gf
            LEFT JOIN eventos e ON e.id = gf.evento_id
            WHERE gf.ativo = 1
            ORDER BY (gf.evento_id IS NULL) ASC, e.data_evento DESC, gf.ordem ASC, gf.id DESC";
    return db()->query($sql)->fetchAll();
}

function buscar_fotos_galeria_admin(): array
{
    $sql = "SELECT gf.*, e.titulo AS evento_titulo
            FROM galeria_fotos gf
            LEFT JOIN eventos e ON e.id = gf.evento_id
            ORDER BY gf.created_at DESC";
    return db()->query($sql)->fetchAll();
}

function buscar_foto_galeria(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM galeria_fotos WHERE id = ?');
    $stmt->execute([$id]);
    $foto = $stmt->fetch();
    return $foto ?: null;
}

// ---------- Loja ----------
/** Formata um CEP de 8 dígitos como "00000-000". Retorna como veio se não tiver 8 dígitos. */
function formatar_cep(?string $cep): string
{
    $digitos = preg_replace('~\D~', '', (string) $cep);
    return strlen($digitos) === 8 ? substr($digitos, 0, 5) . '-' . substr($digitos, 5) : (string) $cep;
}

function formatar_preco(int $centavos): string
{
    return 'R$ ' . number_format($centavos / 100, 2, ',', '.');
}

function buscar_produtos(?string $tipo = null, bool $apenasAtivos = true): array
{
    $sql = 'SELECT * FROM produtos WHERE 1=1';
    $params = [];
    if ($tipo !== null) {
        $sql .= ' AND tipo = ?';
        $params[] = $tipo;
    }
    if ($apenasAtivos) {
        $sql .= ' AND ativo = 1';
    }
    $sql .= ' ORDER BY tipo ASC, nome ASC';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function buscar_produto(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM produtos WHERE id = ?');
    $stmt->execute([$id]);
    $produto = $stmt->fetch();
    return $produto ?: null;
}

function buscar_produto_por_slug(string $slug): ?array
{
    $stmt = db()->prepare('SELECT * FROM produtos WHERE slug = ? AND ativo = 1');
    $stmt->execute([$slug]);
    $produto = $stmt->fetch();
    return $produto ?: null;
}

function produto_slug_em_uso(string $slug, ?int $ignorarId = null): bool
{
    if ($ignorarId) {
        $stmt = db()->prepare('SELECT COUNT(*) FROM produtos WHERE slug = ? AND id != ?');
        $stmt->execute([$slug, $ignorarId]);
    } else {
        $stmt = db()->prepare('SELECT COUNT(*) FROM produtos WHERE slug = ?');
        $stmt->execute([$slug]);
    }
    return (int) $stmt->fetchColumn() > 0;
}

function gerar_slug_unico_produto(string $textoBase, ?int $ignorarId = null): string
{
    $base = gerar_slug($textoBase);
    $slug = $base;
    $i = 2;
    while (produto_slug_em_uso($slug, $ignorarId)) {
        $slug = $base . '-' . $i;
        $i++;
    }
    return $slug;
}

// ---------- Pedidos ----------
/** Código de pedido com componente aleatório (evita enumeração via URL, ex: /pedido-confirmado.php?codigo=...). */
function gerar_codigo_pedido(): string
{
    return 'MC-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
}

function buscar_pedido(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM pedidos WHERE id = ?');
    $stmt->execute([$id]);
    $pedido = $stmt->fetch();
    return $pedido ?: null;
}

function buscar_pedido_por_codigo(string $codigo): ?array
{
    $stmt = db()->prepare('SELECT * FROM pedidos WHERE codigo = ?');
    $stmt->execute([$codigo]);
    $pedido = $stmt->fetch();
    return $pedido ?: null;
}

function buscar_itens_pedido(int $pedidoId): array
{
    $stmt = db()->prepare('SELECT * FROM pedido_itens WHERE pedido_id = ? ORDER BY id ASC');
    $stmt->execute([$pedidoId]);
    return $stmt->fetchAll();
}

/**
 * Verifica se cada item do carrinho ainda cabe no estoque atual (produtos com estoque
 * NULL = ilimitado, nunca bloqueiam). Retorna null se tudo certo, ou uma mensagem de erro
 * com o primeiro item que não couber — usado como última checagem no servidor antes de
 * cobrar, já que o estoque pode ter mudado entre "adicionar ao carrinho" e "finalizar".
 */
function validar_estoque_carrinho(array $itensCarrinho): ?string
{
    foreach ($itensCarrinho as $item) {
        $produto = buscar_produto((int) $item['produto']['id']); // relê o estoque mais atual, não o do momento em que foi ao carrinho
        if (!$produto) {
            return 'Um dos produtos do carrinho não está mais disponível.';
        }
        if ($produto['estoque'] !== null && (int) $item['quantidade'] > (int) $produto['estoque']) {
            return 'Restam apenas ' . (int) $produto['estoque'] . ' unidade(s) de "' . $produto['nome'] . '" em estoque.';
        }
    }
    return null;
}

/**
 * Desconta do estoque os itens de um pedido já pago. Só mexe em produtos com estoque
 * controlado (não NULL). Usa UPDATE...WHERE estoque >= quantidade para ser atômico — se
 * duas compras concorrentes disputarem a última unidade, só uma consegue decrementar aqui;
 * a outra já foi cobrada antes (checkout não é 100% livre dessa janela de corrida em alto
 * volume, mas para o volume desta loja o risco é desprezível) — fica logado para conferência manual.
 */
function baixar_estoque_pedido(int $pedidoId): void
{
    $itens = buscar_itens_pedido($pedidoId);
    foreach ($itens as $item) {
        $produto = buscar_produto((int) $item['produto_id']);
        if (!$produto || $produto['estoque'] === null) {
            continue;
        }
        $stmt = db()->prepare('UPDATE produtos SET estoque = estoque - ? WHERE id = ? AND estoque >= ?');
        $stmt->execute([$item['quantidade'], $produto['id'], $item['quantidade']]);
        if ($stmt->rowCount() === 0) {
            error_log('[estoque] Pedido ' . $pedidoId . ': falha ao baixar estoque de "' . $produto['nome'] . '" (possível concorrência ou estoque já zerado) — conferir manualmente.');
        }
    }
}

/**
 * Envia o e-mail de confirmação de pedido. Ponto único de saída de e-mail transacional —
 * se `mail()` nativo se mostrar pouco confiável em produção, troca-se só esta função por
 * PHPMailer/SMTP sem tocar em quem a chama (checkout-processar.php, webhook).
 */
function enviar_email_pedido(array $pedido): bool
{
    $assunto = 'Pedido ' . $pedido['codigo'] . ' — ' . SITE_NAME;
    $corpo = "Olá, {$pedido['cliente_nome']}!\n\n"
        . "Recebemos seu pedido {$pedido['codigo']}.\n"
        . "Total: " . formatar_preco((int) $pedido['total_centavos']) . "\n"
        . "Status: {$pedido['status']}\n\n"
        . "Qualquer dúvida, responda este e-mail.\n";
    $headers = "From: " . SITE_EMAIL . "\r\n";
    return @mail($pedido['cliente_email'], $assunto, $corpo, $headers);
}

// ---------- Autenticação do admin ----------
function iniciar_sessao(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }
}

function admin_logado(): bool
{
    iniciar_sessao();
    return !empty($_SESSION['admin_logado']);
}

function exigir_login(): void
{
    if (!admin_logado()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function csrf_token(): string
{
    iniciar_sessao();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_validar(?string $token): bool
{
    iniciar_sessao();
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
