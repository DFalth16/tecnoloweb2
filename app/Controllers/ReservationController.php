<?php

namespace App\Controllers;

use App\Services\ReservationService;

class ReservationController
{
    private $service;

    public function __construct(ReservationService $service)
    {
        $this->service = $service;
    }

    public function store(array $requestData)
    {
        try {
            $reservation = $this->service->createReservation($requestData);
            return [
                'status' => 'success',
                'message' => 'Reserva creada exitosamente',
                'data' => $reservation
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
