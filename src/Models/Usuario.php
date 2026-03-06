<?php

namespace EventCore\Models;

//clase usuario del sistema
class Usuario {
    public $id;
    public $id_rol;
    public $nombres;
    public $apellidos;
    public $email;
    public $password_hash;
    public $activo;
    public $creado_en;
    public $nombre_rol; // Virtual field from join

    public function __construct(array $data = []) {
        $this->id            = $data['id_usuario'] ?? null;
        $this->id_rol        = $data['id_rol'] ?? null;
        $this->nombres       = $data['nombres'] ?? '';
        $this->apellidos     = $data['apellidos'] ?? '';
        $this->email         = $data['email'] ?? '';
        $this->password_hash = $data['password_hash'] ?? '';
        $this->activo        = $data['activo'] ?? 1;
        $this->creado_en     = $data['creado_en'] ?? null;
        $this->nombre_rol    = $data['nombre_rol'] ?? '';
    }

    public function getFullName(): string {
        return "{$this->nombres} {$this->apellidos}";
    }
}
