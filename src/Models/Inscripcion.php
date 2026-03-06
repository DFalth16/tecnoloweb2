<?php

namespace EventCore\Models;

//inscripcion de participante 
class Inscripcion
{
    public $id;
    public $id_participante;
    public $id_evento;
    public $fecha_inscripcion;
    public $id_estado_inscripcion;
    public $codigo_inscripcion;
    public $asistio;

    public function __construct(array $data = [])
    {
        $this->id                    = $data['id_inscripcion'] ?? null;
        $this->id_participante       = $data['id_participante'] ?? null;
        $this->id_evento             = $data['id_evento'] ?? null;
        $this->fecha_inscripcion    = $data['fecha_inscripcion'] ?? null;
        $this->id_estado_inscripcion = $data['id_estado_inscripcion'] ?? null;
        $this->codigo_inscripcion    = $data['codigo_inscripcion'] ?? '';
        $this->asistio               = $data['asistio'] ?? 0;
    }
}
