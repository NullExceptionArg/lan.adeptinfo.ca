<?php


namespace App\Repositories\Implementation;


use App\Model\Lan;
use App\Repositories\LanRepository;
use DateTime;

class LanRepositoryImpl implements LanRepository
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
    ): Lan
    {
        $lan = new Lan();
        $lan->lan_start = $lanStart->format('Y-m-d\TH:i:s');
        $lan->lan_end = $lanEnd->format('Y-m-d\TH:i:s');
        $lan->seat_reservation_start = $seatReservationStart->format('Y-m-d\TH:i:s');
        $lan->tournament_reservation_start = $tournamentReservationStart->format('Y-m-d\TH:i:s');
        $lan->event_key_id = $eventKeyId;
        $lan->public_key_id = $publicKeyId;
        $lan->secret_key_id = $secretKeyId;
        $lan->price = $price;
        $lan->rules = $rules;
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