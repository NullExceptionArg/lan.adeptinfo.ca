<?php

namespace App\Services;


use App\Model\Reservation;
use Illuminate\Http\Request;

interface SeatService
{
    public function book(int $lanId, string $seatId): Reservation;
}