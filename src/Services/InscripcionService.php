<?php

namespace EventCore\Services;

use EventCore\Models\Inscripcion;
use EventCore\Repositories\InscripcionRepository;
use EventCore\Services\Interfaces\SubjectInterface;
use EventCore\Services\Interfaces\ObserverInterface;

/**
 * Clase InscripcionService
 * Gestiona la lógica de inscripciones y el patrón Observer.
 */
class InscripcionService implements SubjectInterface
{
    private $repository;
    private $observers = [];

    public function __construct(InscripcionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function attach(ObserverInterface $observer)
    {
        $this->observers[] = $observer;
    }

    public function detach(ObserverInterface $observer)
    {
        $this->observers = array_filter($this->observers, function ($obs) use ($observer) {
            return $obs !== $observer;
        });
    }

    public function notify($data)
    {
        foreach ($this->observers as $observer) {
            $observer->update($data);
        }
    }

    public function createInscripcion(array $data)
    {
        $inscripcion = new Inscripcion($data);
        
        // Generar código único
        $inscripcion->codigo_inscripcion = 'INS-' . strtoupper(substr(md5(uniqid()), 0, 8));

        if ($this->repository->save($inscripcion)) {
            $this->notify($inscripcion);
            return $inscripcion;
        }

        throw new \Exception("Error al crear la inscripción");
    }
}
