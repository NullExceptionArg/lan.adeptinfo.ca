<?php


namespace App\Repositories\Implementation;


use App\Model\Lan;
use App\Model\Reservation;
use App\Repositories\LanRepository;
use DateTime;
use Illuminate\Support\Collection;

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
        float $latitude,
        float $longitude,
        int $places,
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
        $lan->places = $places;
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

    public function getReservedPlaces(int $lanId): int
    {
        return Reservation::where('lan_id', $lanId)->count();
    }

    public function getLans(): ?Collection
    {
        return Lan::all();
    }

    public function removeCurrentLan(): void
    {
        Lan::where('is_current', true)
            ->update(['is_current' => false]);
    }

    public function setCurrentLan(string $lanId): void
    {
        Lan::find($lanId)
            ->update(['is_current' => true]);
    }

    public function getCurrentLan(): ?Lan
    {
        return Lan::where('is_current', true)->first();
    }

    public function updateLanRules(Lan $lan, string $rules): void
    {
        $lan->rules = $rules;
        $lan->save();
    }

    public function updateLanName(Lan $lan, string $name): void
    {
        $lan->name = $name;
        $lan->save();
    }

    public function updateLanPrice(Lan $lan, int $price): void
    {
        $lan->price = $price;
        $lan->save();
    }

    public function updateLanLocation(Lan $lan, float $longitude, float $latitude): void
    {
        $lan->longitude = $longitude;
        $lan->latitude = $latitude;
        $lan->save();
    }

    public function updateLanSeatReservationStart(Lan $lan, DateTime $seatReservationStart): void
    {
        $lan->seat_reservation_start = $seatReservationStart;
        $lan->save();
    }

    public function updateLanTournamentReservationStart(Lan $lan, DateTime $tournamentReservationStart): void
    {
        $lan->tournament_reservation_start = $tournamentReservationStart;
        $lan->save();
    }

    public function updateLanStartDate(Lan $lan, DateTime $lanStart): void
    {
        $lan->lan_start = $lanStart;
        $lan->save();
    }

    public function updateLanEndDate(Lan $lan, DateTime $lanEnd): void
    {
        $lan->lan_end = $lanEnd;
        $lan->save();
    }

    public function updateLanSeatsKeys(
        Lan $lan,
        string $event,
        string $public,
        string $secret
    ): void
    {
        $lan->event_key_id = $event;
        $lan->public_key_id = $public;
        $lan->secret_key_id = $secret;
        $lan->save();
    }

    public function updateLanDescription(Lan $lan, string $description): void
    {
        $lan->description = $description;
        $lan->save();
    }

    public function updateLanPlaces(Lan $lan, int $places): void
    {
        $lan->places = $places;
        $lan->save();
    }
}