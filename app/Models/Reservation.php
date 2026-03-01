<?php

namespace App\Models;

class Reservation
{
    public $id;
    public $id_participante;
    public $id_evento;
    public $fecha_inscripcion;
    public $id_estado_inscripcion;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->id_participante = $data['id_participante'] ?? null;
        $this->id_evento = $data['id_evento'] ?? null;
        $this->fecha_inscripcion = $data['fecha_inscripcion'] ?? null;
        $this->id_estado_inscripcion = $data['id_estado_inscripcion'] ?? null;
    }
}
