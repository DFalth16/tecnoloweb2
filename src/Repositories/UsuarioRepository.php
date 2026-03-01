<?php

namespace EventCore\Repositories;

use EventCore\Models\Usuario;
use PDO;

/**
 * Clase UsuarioRepository
 * Maneja las consultas a la tabla usuarios_admin.
 */
class UsuarioRepository {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findByEmail(string $email): ?Usuario {
        $stmt = $this->db->prepare("
            SELECT u.*, r.nombre_rol 
            FROM usuarios_admin u 
            JOIN roles r ON u.id_rol = r.id_rol 
            WHERE u.email = ? AND u.activo = 1
        ");
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        return $data ? new Usuario($data) : null;
    }

    public function save(Usuario $usuario): bool {
        if ($usuario->id) {
            // Update (not implemented here for brevity, but follows pattern)
            return false;
        } else {
            // Insert
            $stmt = $this->db->prepare("
                INSERT INTO usuarios_admin (id_rol, nombres, apellidos, email, password_hash, activo) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $success = $stmt->execute([
                $usuario->id_rol,
                $usuario->nombres,
                $usuario->apellidos,
                $usuario->email,
                $usuario->password_hash,
                $usuario->activo
            ]);
            
            if ($success) {
                $usuario->id = $this->db->lastInsertId();
            }
            return $success;
        }
    }
}
