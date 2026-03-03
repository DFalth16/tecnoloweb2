<?php

namespace EventCore\Controllers;

use EventCore\Config\Database;
use EventCore\Helpers\SessionHelper;

class UsuarioController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        SessionHelper::requireLogin();
        $usuarios = $this->db->query("
            SELECT u.*, r.nombre_rol AS rol 
            FROM usuarios_admin u 
            JOIN roles r ON u.id_rol = r.id_rol 
            ORDER BY u.creado_en DESC
        ")->fetchAll();
        require ROOT_PATH . '/src/Views/usuarios/index.php';
    }

    public function create() {
        SessionHelper::requireLogin();
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombres   = trim($_POST['nombres'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $pass      = $_POST['password'] ?? '';
            $id_rol    = (int)($_POST['id_rol'] ?? 0);
            
            if (empty($nombres))   $errors[] = 'Los nombres son obligatorios.';
            if (empty($email))     $errors[] = 'El email es obligatorio.';
            if (strlen($pass) < 5) $errors[] = 'La contraseña debe tener al menos 5 caracteres.';
            if ($id_rol < 1)       $errors[] = 'Seleccione un rol válido.';
            
            // Verificar email único
            $stmt = $this->db->prepare("SELECT id_usuario FROM usuarios_admin WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) $errors[] = 'Este email ya está registrado.';
            
            if (empty($errors)) {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $this->db->prepare("
                    INSERT INTO usuarios_admin (id_rol, nombres, apellidos, email, password_hash, activo) 
                    VALUES (?, ?, ?, ?, ?, 1)
                ");
                $stmt->execute([$id_rol, $nombres, $apellidos, $email, $hash]);
                
                SessionHelper::setFlash('success', "Usuario {$nombres} creado correctamente.");
                header('Location: ' . BASE_URL . '/usuarios');
                exit;
            }
        }
        
        $roles = $this->db->query("SELECT * FROM roles ORDER BY id_rol")->fetchAll();
        require ROOT_PATH . '/src/Views/usuarios/crear.php';
    }

    public function edit($id) {
        SessionHelper::requireLogin();
        $id = (int)$id;
        
        $stmt = $this->db->prepare("SELECT * FROM usuarios_admin WHERE id_usuario = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            SessionHelper::setFlash('error', 'Usuario no encontrado.');
            header('Location: ' . BASE_URL . '/usuarios');
            exit;
        }
        
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombres   = trim($_POST['nombres'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $id_rol    = (int)($_POST['id_rol'] ?? 0);
            $activo    = isset($_POST['activo']) ? 1 : 0;
            $pass      = $_POST['password'] ?? '';
            
            if (empty($nombres)) $errors[] = 'Los nombres son obligatorios.';
            if (empty($email))   $errors[] = 'El email es obligatorio.';
            
            if (empty($errors)) {
                if (!empty($pass)) {
                    $hash = password_hash($pass, PASSWORD_DEFAULT);
                    $stmt = $this->db->prepare("
                        UPDATE usuarios_admin SET nombres=?, apellidos=?, email=?, id_rol=?, activo=?, password_hash=? 
                        WHERE id_usuario=?
                    ");
                    $stmt->execute([$nombres, $apellidos, $email, $id_rol, $activo, $hash, $id]);
                } else {
                    $stmt = $this->db->prepare("
                        UPDATE usuarios_admin SET nombres=?, apellidos=?, email=?, id_rol=?, activo=? 
                        WHERE id_usuario=?
                    ");
                    $stmt->execute([$nombres, $apellidos, $email, $id_rol, $activo, $id]);
                }
                
                SessionHelper::setFlash('success', "Usuario actualizado correctamente.");
                header('Location: ' . BASE_URL . '/usuarios');
                exit;
            }
        }
        
        $roles = $this->db->query("SELECT * FROM roles ORDER BY id_rol")->fetchAll();
        require ROOT_PATH . '/src/Views/usuarios/editar.php';
    }

    public function delete($id) {
        SessionHelper::requireLogin();
        $id = (int)$id;
        // Desactivación lógica
        $stmt = $this->db->prepare("UPDATE usuarios_admin SET activo = 0 WHERE id_usuario = ?");
        $stmt->execute([$id]);
        
        SessionHelper::setFlash('success', "Usuario desactivado.");
        header('Location: ' . BASE_URL . '/usuarios');
        exit;
    }
}
