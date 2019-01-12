<?php

namespace App\Services;

use App\Http\Resources\Lan\GetResource;
use App\Http\Resources\Lan\ImageResource;
use App\Http\Resources\Lan\UpdateResource;
use App\Model\Lan;
use DateTime;
use Illuminate\Http\Resources\Json\ResourceCollection;

interface LanService
{
    public function addLanImage(int $lanId, string $image): ImageResource;

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
        ?int $price,
        ?string $rules,
        ?string $description
    ): Lan;

    public function deleteLanImages(string $imageIds): array;

    public function getAll(): ResourceCollection;

    public function get(int $lanId, ?string $fields): GetResource;

    public function setCurrent(int $lanId): Lan;

    public function update(
        int $lanId,
        ?string $name,
        ?DateTime $lanStart,
        ?DateTime $lanEnd,
        ?DateTime $seatReservationStart,
        ?DateTime $tournamentReservationStart,
        ?string $eventKey,
        ?string $publicKey,
        ?string $secretKey,
        ?float $latitude,
        ?float $longitude,
        ?int $places,
        ?int $price,
        ?string $rules,
        ?string $description
    ): UpdateResource;
}
