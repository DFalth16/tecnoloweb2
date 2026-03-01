<?php

namespace EventCore\Middleware;

use EventCore\Helpers\SessionHelper;

/**
 * Clase AuthMiddleware
 * Protege rutas que requieren inicio de sesión.
 */
class AuthMiddleware {
    public static function handle(): void {
        SessionHelper::start();
        if (!SessionHelper::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
}
