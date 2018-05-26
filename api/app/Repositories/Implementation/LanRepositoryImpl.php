<?php


namespace App\Repositories\Implementation;


use App\Model\Lan;
use App\Repositories\LanRepository;
use DateTime;

class LanRepositoryImpl implements LanRepository
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
    ): Lan
    {
        $lan = new Lan();
        $lan->name = $name;
        $lan->lan_start = $lanStart->format('Y-m-d H:i:s');
        $lan->lan_end = $lanEnd->format('Y-m-d H:i:s');
        $lan->seat_reservation_start = $seatReservationStart->format('Y-m-d H:i:s');
        $lan->tournament_reservation_start = $tournamentReservationStart->format('Y-m-d H:i:s');
        $lan->event_key_id = $eventKeyId;
        $lan->public_key_id = $publicKeyId;
        $lan->secret_key_id = $secretKeyId;
        $lan->latitude = $latitude;
        $lan->longitude = $longitude;
        $lan->price = $price;
        $lan->rules = $rules;
        $lan->description = $description;
        $lan->save();

        return $lan;
    }

    public function findLanById(int $id): ?Lan
    {
        return Lan::find($id);
    }

    public function updateLanRules(Lan $lan, string $text): void
    {
        $lan->rules = $text;
        $lan->save();
    }
}