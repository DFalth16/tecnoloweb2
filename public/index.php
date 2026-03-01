<?php
/**
 * Front Controller — EventCore
 */
require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use EventCore\Helpers\SessionHelper;

SessionHelper::start();

// Enrutador básico
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');

switch ($url) {
    case '':
    case 'login':
        $controller = new \EventCore\Controllers\AuthController();
        $controller->login();
        break;

    case 'register':
        $controller = new \EventCore\Controllers\AuthController();
        $controller->register();
        break;

    case 'logout':
        $controller = new \EventCore\Controllers\AuthController();
        $controller->logout();
        break;

    case 'dashboard':
        \EventCore\Middleware\AuthMiddleware::handle();
        // Cargar DashboardController...
        echo "<h1>Dashboard - Próximamente</h1><a href='logout'>Cerrar Sesión</a>";
        break;

    default:
        http_response_code(404);
        echo "404 - Página no encontrada";
        break;
}
