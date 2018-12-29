<?php

namespace App\Repositories;


use App\Model\Lan;
use DateTime;
use Illuminate\Support\Collection;

interface LanRepository
{
    public function create(
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
        bool $hasCurrentPlace,
        ?int $price,
        ?string $rules,
        ?string $description
    ): Lan;

    public function findById(int $id): ?Lan;

    public function getReservedPlaces(int $lanId): int;

    public function getAll(): ?Collection;

    public function removeCurrent(): void;

    public function setCurrent(string $lanId): void;

    public function update(
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