<?php
// Konfigurasi database (default XAMPP/Laragon: user root tanpa password)
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'ummi_kripto');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict']);
    session_start();
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;
    $opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
    if (getenv('DB_DRIVER') === 'sqlite') {          // hanya untuk pengujian
        $pdo = new PDO('sqlite:' . getenv('DB_FILE'), null, null, $opt);
    } else {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, $opt);
    }
    return $pdo;
}

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}

function csrf_check(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Token CSRF tidak valid.');
    }
}
