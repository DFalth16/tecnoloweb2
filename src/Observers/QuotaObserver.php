<?php

namespace EventCore\Observers;

use EventCore\Interfaces\ObserverInterface;

class QuotaObserver implements ObserverInterface
{
    public function update($data)
    {
        // Lógica para actualizar contadores de cupos
        // error_log("Quota update: Evento ID " . $data->id_evento . " tiene un nuevo inscrito.");
    }
}
