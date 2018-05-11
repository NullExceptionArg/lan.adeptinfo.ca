<?php

namespace App\Repositories;


use App\Model\Lan;
use App\Model\Reservation;
use Illuminate\Contracts\Auth\Authenticatable;

interface SeatRepository
{
    public function attachLanUser(Authenticatable $user, Lan $lan, string $seatId): void;

    public function findReservationByLanIdAndUserId(int $userId, int $lanId): ?Reservation;

    public function findReservationByLanIdAndSeatId(string $seatId, int $lanId): ?Reservation;
}