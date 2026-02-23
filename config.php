<?php
/**
 * EventCore — Configuración y Conexión a Base de Datos
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Configuración de BD ──
define('DB_HOST', 'localhost');
define('DB_NAME', 'drop');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('BASE_URL', '/drop');

// ── Conexión PDO ──
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

// ── Helpers de sesión ──
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function currentUser(): array {
    return [
        'id'       => $_SESSION['user_id']       ?? 0,
        'nombres'  => $_SESSION['user_nombres']   ?? '',
        'apellidos'=> $_SESSION['user_apellidos']  ?? '',
        'email'    => $_SESSION['user_email']      ?? '',
        'rol'      => $_SESSION['user_rol']        ?? '',
        'id_rol'   => $_SESSION['user_id_rol']     ?? 0,
    ];
}

function isAdmin(): bool {
    return ($_SESSION['user_id_rol'] ?? 0) == 1;
}

function isOrganizador(): bool {
    return ($_SESSION['user_id_rol'] ?? 0) == 2;
}

function hasPermission(array $rolesPermitidos): bool {
    return in_array($_SESSION['user_id_rol'] ?? 0, $rolesPermitidos);
}

function requirePermission(array $rolesPermitidos): void {
    if (!hasPermission($rolesPermitidos)) {
        header('Location: ' . BASE_URL . '/index.php?error=permisos');
        exit;
    }
}

// ── Flash messages ──
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ── Helpers generales ──
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function generateCode(string $prefix = 'EVT'): string {
    return $prefix . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
}
