<?php

namespace App\Repositories\Implementation;


use App\Model\Lan;
use App\Model\Reservation;
use App\Model\User;
use App\Repositories\SeatRepository;
use Illuminate\Contracts\Auth\Authenticatable;

class SeatRepositoryImpl implements SeatRepository
{
    public function attachLanUser(Authenticatable $user, Lan $lan, string $seatId): void
    {
        $lan->user()->attach($user->id, [
            $seatId
        ]);
    }

    public function findReservationByLanAndUserId(int $userId, int $lanId): Reservation
    {
        return Reservation::find([$userId, $lanId]);
    }
}