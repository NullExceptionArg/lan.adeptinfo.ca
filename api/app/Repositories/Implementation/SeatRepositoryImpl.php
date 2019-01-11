<?php

namespace App\Repositories\Implementation;

use App\Model\Reservation;
use App\Repositories\SeatRepository;
use Carbon\Carbon;
use Illuminate\{Support\Collection, Support\Facades\DB};

class SeatRepositoryImpl implements SeatRepository
{
    public function createReservation(int $userId, int $lanId, string $seatId): int
    {
        return DB::table('reservation')
            ->insertGetId([
                'user_id' => $userId,
                'lan_id' => $lanId,
                'seat_id' => $seatId
            ]);
    }

    public function deleteReservation(int $reservationId): void
    {
        Reservation::where('id', $reservationId)
            ->delete();
    }

    public function findReservationByLanIdAndSeatId(int $lanId, string $seatId): ?Reservation
    {
        return Reservation::where('lan_id', $lanId)
            ->where('seat_id', $seatId)
            ->where('deleted_at', null)
            ->first();
    }

    public function findReservationByLanIdAndUserId(int $lanId, int $userId): ?Reservation
    {
        return Reservation::where('user_id', $userId)
            ->where('lan_id', $lanId)
            ->where('deleted_at', null)
            ->first();
    }

    public function getCurrentSeat(int $userId, int $lanId): ?Reservation
    {
        return Reservation::where('user_id', $userId)
            ->where('lan_id', $lanId)
            ->where('deleted_at', null)
            ->first();
    }

    public function getSeatHistoryForUser(int $userId, int $lanId): ?Collection
    {
        return Reservation::withTrashed()
            ->where('user_id', $userId)
            ->where('lan_id', $lanId)
            ->get();
    }

    public function setReservationArrived(string $reservationId, int $lanId): void
    {
        DB::table('reservation')
            ->where('seat_id', $lanId)
            ->where('seat_id', $reservationId)
            ->update([
                'arrived_at' => Carbon::now()
            ]);
    }

    public function setReservationLeft(string $reservationId, int $lanId): void
    {
        DB::table('reservation')
            ->where('lan_id', $lanId)
            ->where('seat_id', $reservationId)
            ->update([
                'left_at' => Carbon::now()
            ]);
    }
}
