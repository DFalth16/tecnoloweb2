<?php

namespace EventCore\Models;

class Participante
{
    public $id_participante;
    public $nombres;
    public $apellidos;
    public $email;
    public $telefono;
    public $documento_id;
    public $creado_en;

    public function __construct(array $data = [])
    {
        $this->id_participante = $data['id_participante'] ?? null;
        $this->nombres         = $data['nombres']         ?? '';
        $this->apellidos       = $data['apellidos']       ?? '';
        $this->email           = $data['email']           ?? '';
        $this->telefono        = $data['telefono']        ?? '';
        $this->documento_id    = $data['documento_id']    ?? '';
        $this->creado_en       = $data['creado_en']       ?? null;
    }

    public function getNombreCompleto(): string
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }
}
