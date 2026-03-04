<?php

define('BASE_URL', 'http://localhost/cocu_system/public/');
define('DB_HOST', 'localhost');
define('DB_NAME', 'cocu_db');
define('DB_USER', 'root');
define('DB_PASS', '');

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token(): string
{
    return $_SESSION['csrf_token'] ?? '';
}

function csrf_field(): void
{
    $token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function verify_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $postedToken = $_POST['csrf_token'] ?? '';

    if (!$sessionToken || !$postedToken || !hash_equals($sessionToken, $postedToken)) {
        http_response_code(400);
        exit('Invalid CSRF token');
    }
}

?>