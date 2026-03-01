<?php

namespace EventCore\Observers;

use EventCore\Interfaces\ObserverInterface;

class EmailObserver implements ObserverInterface
{
    public function update($data)
    {
        // Lógica para enviar email al participante
        // error_log("Simular Envío Email: Se ha confirmado tu reserva ID: " . $data->id);
    }
}
