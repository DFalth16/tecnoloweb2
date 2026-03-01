<?php

namespace EventCore\Repositories;

use EventCore\Models\Inscripcion;
use PDO;

/**
 * Clase InscripcionRepository
 * Maneja las consultas a la tabla inscripciones.
 */
class InscripcionRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function save(Inscripcion $inscripcion)
    {
        if ($inscripcion->id) {
            // Update
            return false;
        } else {
            // Insert
            $stmt = $this->db->prepare("
                INSERT INTO inscripciones (id_evento, id_participante, id_estado_inscripcion, codigo_inscripcion, fecha_inscripcion)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $success = $stmt->execute([
                $inscripcion->id_evento,
                $inscripcion->id_participante,
                $inscripcion->id_estado_inscripcion ?: 1,
                $inscripcion->codigo_inscripcion,
                $inscripcion->fecha_inscripcion ?: date('Y-m-d H:i:s')
            ]);

            if ($success) {
                $inscripcion->id = $this->db->lastInsertId();
            }
            return $success;
        }
    }
}
