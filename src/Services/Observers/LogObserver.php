<?php

namespace EventCore\Services\Observers;

use EventCore\Services\Interfaces\ObserverInterface;

class LogObserver implements ObserverInterface
{
    public function update($data)
    {
        // Lógica para registrar en logs de auditoría
        // error_log("Audit Log: Nueva reserva creada para el evento ID: " . $data->id_evento);
    }
}
