<?php

namespace EventCore\Controllers;

use EventCore\Services\InscripcionService;
use EventCore\Repositories\InscripcionRepository;
use EventCore\Observers\EmailObserver;
use EventCore\Observers\LogObserver;
use EventCore\Observers\QuotaObserver;
use EventCore\Config\Database;

/**
 * Clase InscripcionController
 * Recibe peticiones de inscripciones.
 */
class InscripcionController
{
    private $service;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $repository = new InscripcionRepository($db);
        $this->service = new InscripcionService($repository);

        // Adjuntar observadores
        $this->service->attach(new EmailObserver());
        $this->service->attach(new LogObserver());
        $this->service->attach(new QuotaObserver());
    }

    public function store(array $requestData)
    {
        try {
            $inscripcion = $this->service->createInscripcion($requestData);
            return [
                'status' => 'success',
                'message' => 'Inscripción creada exitosamente',
                'data' => $inscripcion
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
