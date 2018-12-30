<?php

namespace App\Repositories;


use App\Model\Image;
use App\Model\Lan;
use DateTime;
use Illuminate\Support\Collection;

interface LanRepository
{
    public function createImageForLan(int $lanId, string $image): int;

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
    ): int;

    public function deleteImages(array $imageId): void;

    public function findById(int $id): ?Lan;

    public function findImageById(int $imageId): ?Image;

    public function getAll(): ?Collection;

    public function getImagesForLan(int $lanId): Collection;

    public function getReservedPlaces(int $lanId): int;

    public function removeCurrent(): void;

    public function setCurrent(string $lanId): void;

    public function update(
        int $lanId,
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
    ): void;
}