<?php


namespace App\Repositories;


use App\Model\Lan;
use DateTime;

interface LanRepository
{
    public function createLan(
        DateTime $lanStart,
        DateTime $lanEnd,
        DateTime $seatReservationStart,
        DateTime $tournamentReservationStart,
        string $eventKeyId,
        string $publicKeyId,
        string $secretKeyId,
        ?int $price,
        ?string $rules
    ): Lan;

    public function findLanById(int $id): ?Lan;

    public function updateLanRules(Lan $lan, string $text): void;

    public function getRules(Lan $lan): string;
}