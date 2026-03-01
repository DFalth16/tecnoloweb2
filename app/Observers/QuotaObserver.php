<?php

namespace App\Observers;

use App\Interfaces\ObserverInterface;
use PDO;

class QuotaObserver implements ObserverInterface
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function update($reservation)
    {
        // Actualizar el cupo del evento en la base de datos
        $stmt = $this->db->prepare("UPDATE eventos SET cupo_maximo = cupo_maximo - 1 WHERE id_evento = ?");
        $stmt->execute([$reservation->id_evento]);

        $logMessage = "[QuotaObserver] Cupo actualizado para el evento ID: {$reservation->id_evento}\n";
        file_put_contents(__DIR__ . '/../../app.log', $logMessage, FILE_APPEND);
    }
}
