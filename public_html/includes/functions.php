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
