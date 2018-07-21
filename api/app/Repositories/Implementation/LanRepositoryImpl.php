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
    ): Lan
    {
        $lan = new Lan();
        $lan->name = $name;
        $lan->lan_start = $lanStart->format('Y-m-d H:i:s');
        $lan->lan_end = $lanEnd->format('Y-m-d H:i:s');
        $lan->seat_reservation_start = $seatReservationStart->format('Y-m-d H:i:s');
        $lan->tournament_reservation_start = $tournamentReservationStart->format('Y-m-d H:i:s');
        $lan->event_key = $eventKey;
        $lan->public_key = $publicKey;
        $lan->secret_key = $secretKey;
        $lan->latitude = $latitude;
        $lan->longitude = $longitude;
        $lan->places = $places;
        $lan->is_current = $hasCurrentPlace;
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

    public function updateLan(
        Lan $lan,
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
    ): Lan
    {
        $lan->name = $name;
        $lan->lan_start = $lanStart->format('Y-m-d H:i:s');
        $lan->lan_end = $lanEnd->format('Y-m-d H:i:s');
        $lan->seat_reservation_start = $seatReservationStart->format('Y-m-d H:i:s');
        $lan->tournament_reservation_start = $tournamentReservationStart->format('Y-m-d H:i:s');
        $lan->event_key = $eventKey;
        $lan->public_key = $publicKey;
        $lan->secret_key = $secretKey;
        $lan->latitude = $latitude;
        $lan->longitude = $longitude;
        $lan->places = $places;
        $lan->price = $price;
        $lan->rules = $rules;
        $lan->description = $description;
        $lan->save();

        return $lan;
    }
}