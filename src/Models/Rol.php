<?php

namespace EventCore\Models;

/**
 * Clase Rol
 * Representa un rol de usuario en el sistema.
 */
class Rol {
    public $id;
    public $nombre;
    public $descripcion;

    public function __construct(array $data = []) {
        $this->id          = $data['id_rol'] ?? null;
        $this->nombre      = $data['nombre_rol'] ?? '';
        $this->descripcion = $data['descripcion'] ?? '';
    }
}
