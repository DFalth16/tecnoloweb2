<?php

namespace App\Observers;

use App\Interfaces\ObserverInterface;

class EmailObserver implements ObserverInterface
{
    public function update($reservation)
    {
        // Simulando envío de correo
        $logMessage = "[EmailObserver] Enviando confirmación de reserva #{$reservation->id} al participante #{$reservation->id_participante}\n";
        file_put_contents(__DIR__ . '/../../app.log', $logMessage, FILE_APPEND);
    }
}
