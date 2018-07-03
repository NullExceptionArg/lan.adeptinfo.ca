<?php


namespace App\Repositories;


use App\Model\Lan;
use DateTime;
use Illuminate\Support\Collection;

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
        float $latitude,
        float $longitude,
        int $places,
        ?int $price,
        ?string $rules,
        ?string $description
    ): Lan;

    public function findLanById(int $id): ?Lan;

    public function updateLanRules(Lan $lan, string $text): void;

    public function getReservedPlaces(int $lanId): int;

    public function getLans(): ?Collection;
}