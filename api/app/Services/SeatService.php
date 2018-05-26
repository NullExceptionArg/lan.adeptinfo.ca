<?php

namespace App\Services;


use App\Model\Reservation;

interface SeatService
{
    public function book(string $lanId, string $seatId): Reservation;

    public function confirmArrival(string $lanId, string $seatId): Reservation;

    public function unConfirmArrival(string $lanId, string $seatId): Reservation;
}