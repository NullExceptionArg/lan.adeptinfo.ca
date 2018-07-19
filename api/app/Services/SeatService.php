<?php

namespace App\Services;


use App\Model\Reservation;
use Illuminate\Http\Request;

interface SeatService
{
    public function book(Request $request, string $seatId): Reservation;

    public function confirmArrival(Request $request, string $seatId): Reservation;

    public function unConfirmArrival(Request $request, string $seatId): Reservation;

    public function assign(Request $request, string $seatId): Reservation;
}