<?php

namespace App\Services;

use App\Model\Reservation;

/**
 * Méthodes pour exécuter la logique d'affaire des sièges.
 *
 * Interface SeatService
 * @package App\Services<
 */
interface SeatService
{
    public function assign(int $lanId, string $email, string $seatId): Reservation;

    public function book(int $lanId, string $seatId): Reservation;

    public function confirmArrival(int $lanId, string $seatId): Reservation;

    public function unAssign(int $lanId, string $email, string $seatId): Reservation;

    public function unBook(int $lanId, string $seatId): Reservation;

    public function unConfirmArrival(int $lanId, string $seatId): Reservation;
}
