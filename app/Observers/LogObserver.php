<?php

namespace App\Observers;

use App\Interfaces\ObserverInterface;

class LogObserver implements ObserverInterface
{
    public function update($reservation)
    {
        $logMessage = "[" . date('Y-m-d H:i:s') . "] [LogObserver] Nueva reserva creada: ID: {$reservation->id}, Evento: {$reservation->id_evento}\n";
        file_put_contents(__DIR__ . '/../../app.log', $logMessage, FILE_APPEND);
    }
}
