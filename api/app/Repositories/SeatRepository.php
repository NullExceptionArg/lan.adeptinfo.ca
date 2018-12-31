<?php

namespace App\Repositories;

use App\Model\Reservation;
use Illuminate\Support\Collection;

interface SeatRepository
{
    public function createReservation(int $userId, int $lanId, string $seatId): int;

    public function deleteReservation(int $reservationId): void;

    public function findReservationByLanIdAndSeatId(int $lanId, string $seatId): ?Reservation;

    public function findReservationByLanIdAndUserId(int $lanId, int $userId): ?Reservation;

    public function getCurrentSeat(int $userId, int $lanId): ?Reservation;

    public function getSeatHistoryForUser(int $userId, int $lanId): ?Collection;

    public function setReservationArrived(string $reservationId, int $lanId): void;

    public function setReservationLeft(string $reservationId, int $lanId): void;
}
