<?php

namespace App\Repositories;


use App\Model\Reservation;

interface SeatRepository
{
    public function findReservationByLanIdAndUserId(int $lanId, int $userId): ?Reservation;

    public function findReservationByLanIdAndSeatId(int $lanId, string $seatId): ?Reservation;

    public function createReservation(int $userId, int $lanId, string $seatId): Reservation;

}