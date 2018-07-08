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
        string $eventKey,
        string $publicKey,
        string $secretKey,
        float $latitude,
        float $longitude,
        int $places,
        ?int $price,
        ?string $rules,
        ?string $description
    ): Lan;

    public function findLanById(int $id): ?Lan;

    public function getReservedPlaces(int $lanId): int;

    public function getLans(): ?Collection;

    public function removeCurrentLan(): void;

    public function setCurrentLan(string $lanId): void;

    public function getCurrentLan(): ?Lan;

    public function updateLan(
        Lan $lan,
        string $name,
        DateTime $lanStart,
        DateTime $lanEnd,
        DateTime $seatReservationStart,
        DateTime $tournamentReservationStart,
        string $eventKey,
        string $publicKey,
        string $secretKey,
        float $latitude,
        float $longitude,
        int $places,
        ?int $price,
        ?string $rules,
        ?string $description
    ): Lan;
}