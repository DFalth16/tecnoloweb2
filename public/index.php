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
        $controller = new \EventCore\Controllers\DashboardController();
        $controller->index();
        break;

    case 'eventos':
        \EventCore\Middleware\AuthMiddleware::handle();
        $controller = new \EventCore\Controllers\EventoController();
        $action = $_GET['action'] ?? 'index';
        $id = $_GET['id'] ?? null;
        
        if ($action === 'crear') {
            $controller->create();
        } elseif ($action === 'editar' && $id) {
            $controller->edit($id);
        } elseif ($action === 'eliminar' && $id) {
            $controller->delete($id);
        } else {
            $controller->index();
        }
        break;

    case 'usuarios':
        \EventCore\Middleware\AuthMiddleware::handle();
        $controller = new \EventCore\Controllers\UsuarioController();
        $action = $_GET['action'] ?? 'index';
        $id = $_GET['id'] ?? null;
        
        switch($action) {
            case 'crear':    $controller->create(); break;
            case 'editar':   $controller->edit($id); break;
            case 'eliminar': $controller->delete($id); break;
            default:         $controller->index(); break;
        }
        break;

    case 'sedes':
        \EventCore\Middleware\AuthMiddleware::handle();
        $controller = new \EventCore\Controllers\SedeController();
        $action = $_GET['action'] ?? 'index';
        $id = $_GET['id'] ?? null;
        
        switch($action) {
            case 'crear':    $controller->create(); break;
            case 'editar':   $controller->edit($id); break;
            case 'eliminar': $controller->delete($id); break;
            default:         $controller->index(); break;
        }
        break;

    default:
        http_response_code(404);
        echo "404 - Página no encontrada";
        break;
}
