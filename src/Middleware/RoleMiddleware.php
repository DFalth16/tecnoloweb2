<?php

namespace EventCore\Middleware;

use EventCore\Helpers\SessionHelper;

/**
 * Clase RoleMiddleware
 * Verifica permisos basados en el rol del usuario.
 */
class RoleMiddleware {
    public static function handle(array $allowedRoles): void {
        SessionHelper::start();
        $userRoleId = SessionHelper::get('user_id_rol');

        if (!in_array($userRoleId, $allowedRoles)) {
            header('Location: ' . BASE_URL . '/dashboard?error=access_denied');
            exit;
        }
    }
}
