<?php

namespace App\Services;

use App\Interfaces\SubjectInterface;
use App\Interfaces\ObserverInterface;
use App\Models\Reservation;
use App\Repositories\ReservationRepository;

class ReservationService implements SubjectInterface
{
    private $observers = [];
    private $repository;

    public function __construct(ReservationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function attach(ObserverInterface $observer)
    {
        $this->observers[] = $observer;
    }

    public function detach(ObserverInterface $observer)
    {
        $this->observers = array_filter($this->observers, function ($o) use ($observer) {
            return $o !== $observer;
        });
    }

    public function notify($reservation)
    {
        foreach ($this->observers as $observer) {
            $observer->update($reservation);
        }
    }

    public function createReservation(array $data)
    {
        $reservation = new Reservation($data);
        
        // 1. Guardar en Base de Datos (Uso de Repository)
        $savedReservation = $this->repository->save($reservation);

        // 2. Notificar a los observadores (Observer Pattern)
        $this->notify($savedReservation);

        return $savedReservation;
    }
}
