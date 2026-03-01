<?php

namespace App\Repositories;

use App\Models\Reservation;
use PDO;

class ReservationRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function save(Reservation $reservation)
    {
        $stmt = $this->db->prepare("
            INSERT INTO inscripciones (id_participante, id_evento, fecha_inscripcion, id_estado_inscripcion)
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $reservation->id_participante,
            $reservation->id_evento,
            $reservation->fecha_inscripcion ?: date('Y-m-d H:i:s'),
            $reservation->id_estado_inscripcion ?: 1 // Default state
        ]);

        $reservation->id = $this->db->lastInsertId();
        return $reservation;
    }
}
