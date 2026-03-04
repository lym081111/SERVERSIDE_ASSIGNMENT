<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

require_once "../config/config.php";

// Autoload Controllers & Models
spl_autoload_register(function ($class) {
    if (file_exists("../app/controllers/$class.php")) {
        require_once "../app/controllers/$class.php";
    } elseif (file_exists("../app/models/$class.php")) {
        require_once "../app/models/$class.php";
    }
});

// Basic Routing
$rawUrl = $_GET['url'] ?? 'auth/login';
$url = explode('/', $rawUrl);

$controllerSegment = $url[0] ?? 'auth';
$methodSegment = $url[1] ?? 'login';

if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $controllerSegment)) {
    $controllerSegment = 'auth';
}

if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $methodSegment)) {
    $methodSegment = 'login';
}

$controllerName = ucfirst($controllerSegment) . "Controller";
$method = $methodSegment ?: 'index';

if (class_exists($controllerName)) {
    $controller = new $controllerName();
    if (is_callable([$controller, $method])) {
        $controller->$method();
    } else {
        echo "Method not found.";
    }
} else {
    echo "Controller not found.";
}

?>