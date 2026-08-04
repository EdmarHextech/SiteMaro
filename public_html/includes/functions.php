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

/** Gera um slug (URL amigável) a partir de um texto livre, ex: "Título Ótimo!" -> "titulo-otimo". */
function gerar_slug(string $texto): string
{
    $transliterado = @iconv('UTF-8', 'ASCII//TRANSLIT', $texto);
    if ($transliterado === false) {
        $transliterado = $texto;
    }
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
