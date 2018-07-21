<?php

namespace App\Repositories;


use App\Model\Lan;
use App\Model\Reservation;
use App\Model\User;
use Illuminate\Support\Collection;

interface SeatRepository
{
    public function findReservationByLanIdAndUserId(int $lanId, int $userId): ?Reservation;

    public function findReservationByLanIdAndSeatId(int $lanId, string $seatId): ?Reservation;

    public function createReservation(int $userId, int $lanId, string $seatId): Reservation;

    public function getCurrentSeat(User $user, Lan $lan): ?Reservation;

    public function getSeatHistoryForUser(User $user, Lan $lan): ?Collection;

    public function setReservationArrived(Reservation $reservation): void;

    public function setReservationLeft(Reservation $reservation): void;

    public function deleteReservation(Reservation $reservation): void;
}