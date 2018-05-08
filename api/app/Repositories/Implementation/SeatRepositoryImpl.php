<?php

namespace App\Repositories\Implementation;


use App\Model\Lan;
use App\Model\Reservation;
use App\Repositories\SeatRepository;
use Illuminate\Contracts\Auth\Authenticatable;

class SeatRepositoryImpl implements SeatRepository
{
    public function attachLanUser(Authenticatable $user, Lan $lan, string $seatId): void
    {
        $lan->user()->attach($user->id, [
            "seat_id" => $seatId
        ]);
    }

    public function findReservationByLanAndUserId(int $userId, int $lanId): Reservation
    {
        return Reservation::where('user_id', $userId)
            ->where('lan_id', $lanId)->first();
    }
}