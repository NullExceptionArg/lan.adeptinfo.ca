<?php

namespace App\Services;


use App\Model\Reservation;
use Illuminate\Http\Request;

interface SeatService
{
    public function book(Request $input): Reservation;
}