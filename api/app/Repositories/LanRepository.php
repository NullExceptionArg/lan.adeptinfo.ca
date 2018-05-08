<?php


namespace App\Repositories;


use App\Model\Lan;
use DateTime;
use Illuminate\Contracts\Auth\Authenticatable;

interface LanRepository
{
    public function createLan(
        DateTime $lanStart,
        DateTime $lanEnd,
        DateTime $reservationStart,
        DateTime $tournamentStart,
        string $eventKeyId,
        string $publicKeyId,
        string $secretKeyId,
        int $price
    ): Lan;

    public function findById(int $id): ?Lan;

    public function attachUserLan(Authenticatable $user, Lan $lan, string $seatId): void;
}