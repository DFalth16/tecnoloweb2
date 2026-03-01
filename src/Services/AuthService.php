<?php

namespace EventCore\Services;

use EventCore\Models\Usuario;
use EventCore\Repositories\UsuarioRepository;
use EventCore\Helpers\SessionHelper;

/**
 * Clase AuthService
 * Centraliza la lógica de autenticación.
 */
class AuthService {
    private $repository;

    public function __construct(UsuarioRepository $repository) {
        $this->repository = $repository;
    }

    public function login(string $email, string $password): bool {
        $usuario = $this->repository->findByEmail($email);

        if ($usuario && password_verify($password, $usuario->password_hash)) {
            SessionHelper::set('user_id', $usuario->id);
            SessionHelper::set('user_nombres', $usuario->nombres);
            SessionHelper::set('user_apellidos', $usuario->apellidos);
            SessionHelper::set('user_email', $usuario->email);
            SessionHelper::set('user_rol', $usuario->nombre_rol);
            SessionHelper::set('user_id_rol', $usuario->id_rol);
            return true;
        }

        return false;
    }

    public function logout(): void {
        SessionHelper::destroy();
    }
}
