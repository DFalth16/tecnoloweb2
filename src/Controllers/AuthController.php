<?php

namespace EventCore\Controllers;

use EventCore\Services\AuthService;
use EventCore\Repositories\UsuarioRepository;
use EventCore\Repositories\RolRepository;
use EventCore\Models\Usuario;
use EventCore\Helpers\SessionHelper;
use EventCore\Helpers\Validator;
use EventCore\Config\Database;

/**
 * Clase AuthController
 * Gestiona el inicio de sesión, cierre de sesión y registro.
 */
class AuthController {
    private $authService;
    private $usuarioRepository;
    private $rolRepository;

    public function __construct() {
        $db = Database::getInstance()->getConnection();
        $this->usuarioRepository = new UsuarioRepository($db);
        $this->rolRepository = new RolRepository($db);
        $this->authService = new AuthService($this->usuarioRepository);
    }

    public function login() {
        if (SessionHelper::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->authService->login($email, $password)) {
                header('Location: ' . BASE_URL . '/dashboard');
                exit;
            } else {
                $error = 'Credenciales incorrectas o cuenta inactiva.';
            }
        }

        require ROOT_PATH . '/src/Views/auth/login.php';
    }

    public function register() {
        $error = '';
        $success = '';
        $roles = $this->rolRepository->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombres'   => Validator::sanitize($_POST['nombres'] ?? ''),
                'apellidos' => Validator::sanitize($_POST['apellidos'] ?? ''),
                'email'     => Validator::sanitize($_POST['email'] ?? ''),
                'password'  => $_POST['password'] ?? '',
                'id_rol'    => $_POST['id_rol'] ?? ''
            ];

            $errors = Validator::required($data, ['nombres', 'apellidos', 'email', 'password', 'id_rol']);
            
            if (empty($errors)) {
                if (!Validator::email($data['email'])) {
                    $error = 'Email no válido.';
                } elseif ($this->usuarioRepository->findByEmail($data['email'])) {
                    $error = 'El email ya está registrado.';
                } else {
                    $usuario = new Usuario();
                    $usuario->nombres = $data['nombres'];
                    $usuario->apellidos = $data['apellidos'];
                    $usuario->email = $data['email'];
                    $usuario->id_rol = $data['id_rol'];
                    $usuario->password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
                    $usuario->activo = 1;

                    if ($this->usuarioRepository->save($usuario)) {
                        $success = 'Registro exitoso. Ya puedes iniciar sesión.';
                    } else {
                        $error = 'Error al registrar usuario.';
                    }
                }
            } else {
                $error = implode(' ', $errors);
            }
        }

        require ROOT_PATH . '/src/Views/auth/register.php';
    }

    public function logout() {
        $this->authService->logout();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
