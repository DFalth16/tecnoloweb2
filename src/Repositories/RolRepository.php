<?php

namespace EventCore\Repositories;

use EventCore\Models\Rol;
use PDO;

/**
 * Clase RolRepository
 * Consultas para la tabla roles.
 */
class RolRepository {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM roles");
        $roles = [];
        while ($data = $stmt->fetch()) {
            $roles[] = new Rol($data);
        }
        return $roles;
    }
}
