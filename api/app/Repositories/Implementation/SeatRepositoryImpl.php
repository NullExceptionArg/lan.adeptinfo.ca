<?php

namespace App\Repositories\Implementation;


use App\Model\Reservation;
use App\Model\User;
use App\Repositories\SeatRepository;
use Illuminate\Support\Collection;

class SeatRepositoryImpl implements SeatRepository
{

    public function findReservationByLanIdAndUserId(int $lanId, int $userId): ?Reservation
    {
        return Reservation::where('user_id', $userId)
            ->where('lan_id', $lanId)->first();
    }

    public function findReservationByLanIdAndSeatId(int $lanId, string $seatId): ?Reservation
    {
        return Reservation::where('lan_id', $lanId)
            ->where('seat_id', $seatId)->first();
    }

    public function createReservation(int $userId, int $lanId, string $seatId): Reservation
    {
        $reservation = new Reservation();
        $reservation->user_id = $userId;
        $reservation->lan_id = $lanId;
        $reservation->seat_id = $seatId;
        $reservation->save();

        return $reservation;
    }

    public function getCurrentSeat(User $user): Reservation
    {
        return Reservation::where('user_id', $user->id)
            ->where('soft_delete', null)
            ->first();
    }

    public function getSeatHistoryForUser(User $user): Collection
    {
        // TODO: Implement getSeatHistoryForUser() method.
    }
}