<?php


namespace App\Services;


use App\Model\Lan;
use App\Model\Reservation;
use Illuminate\Http\Request;

interface LanService
{
    public function createLan(Request $input): Lan;

    public function createReservation(Request $input): Reservation;
}