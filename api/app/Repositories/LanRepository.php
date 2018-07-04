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

    public function getReservedPlaces(int $lanId): int;

    public function getLans(): ?Collection;

    public function removeCurrentLan(): void;

    public function setCurrentLan(string $lanId): void;

    public function getCurrentLan(): ?Lan;

    public function updateLanRules(Lan $lan, string $rules): void;

    public function updateLanName(Lan $lan, string $name): void;

    public function updateLanPrice(Lan $lan, int $price): void;

    public function updateLanLocation(Lan $lan, float $longitude, float $latitude): void;

    public function updateLanSeatReservationStart(Lan $lan, DateTime $seatReservationStart): void;

    public function updateLanTournamentReservationStart(Lan $lan, DateTime $tournamentReservationStart): void;

    public function updateLanStartDate(Lan $lan, DateTime $lanStart): void;

    public function updateLanEndDate(Lan $lan, DateTime $lanEnd): void;

    public function updateLanSeatsKeys(
        Lan $lan,
        string $event,
        string $public,
        string $secret
    ): void;

    public function updateLanDescription(Lan $lan, string $description): void;

    public function updateLanPlaces(Lan $lan, int $description): void;
}