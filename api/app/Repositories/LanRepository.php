<?php


namespace App\Repositories;


use App\Model\Lan;
use DateTime;

interface LanRepository
{
    public function createLan(
        string $name,
        DateTime $lanStart,
        DateTime $lanEnd,
        DateTime $seatReservationStart,
        DateTime $tournamentReservationStart,
        string $eventKeyId,
        string $publicKeyId,
        string $secretKeyId,
        float $longitude,
        float $latitude,
        ?int $price,
        ?string $rules,
        ?string $description
    ): Lan;

    public function findLanById(int $id): ?Lan;

    public function updateLanRules(Lan $lan, string $text): void;
}