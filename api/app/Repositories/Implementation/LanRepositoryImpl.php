<?php


namespace App\Repositories\Implementation;


use App\Model\Lan;
use App\Model\Reservation;
use App\Repositories\LanRepository;
use DateTime;
use Illuminate\Support\Collection;

class LanRepositoryImpl implements LanRepository
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

    public function findById(int $id): ?Lan
    {
        return Lan::find($id);
    }

    public function getReservedPlaces(int $lanId): int
    {
        return Reservation::where('lan_id', $lanId)->count();
    }

    public function getAll(): ?Collection
    {
        return Lan::all();
    }

    public function removeCurrent(): void
    {
        Lan::where('is_current', true)
            ->update(['is_current' => false]);
    }

    public function setCurrent(string $lanId): void
    {
        Lan::find($lanId)
            ->update(['is_current' => true]);
    }

    public function getCurrent(): ?Lan
    {
        return Lan::where('is_current', true)->first();
    }

    public function update(
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
        $lan->name = $name != null ? $name : $lan->name;
        $lan->lan_start = $lanStart != null ? $lanStart->format('Y-m-d H:i:s') : $lan->lan_start->format('Y-m-d H:i:s');
        $lan->lan_end = $lanEnd != null ? $lanEnd->format('Y-m-d H:i:s') : $lan->lan_end->format('Y-m-d H:i:s');
        $lan->seat_reservation_start = $seatReservationStart != null ? $seatReservationStart->format('Y-m-d H:i:s') : $lan->seat_reservation_start->format('Y-m-d H:i:s');
        $lan->tournament_reservation_start = $tournamentReservationStart != null ? $tournamentReservationStart->format('Y-m-d H:i:s') : $lan->tournament_reservation_start->format('Y-m-d H:i:s');
        $lan->event_key = $eventKey != null ? $eventKey : $lan->event_key;
        $lan->public_key = $publicKey != null ? $publicKey : $lan->public_key;
        $lan->secret_key = $secretKey != null ? $secretKey : $lan->secret_key;
        $lan->latitude = $latitude != null ? $latitude : $lan->latitude;
        $lan->longitude = $longitude != null ? $longitude : $lan->longitude;
        $lan->places = $places != null ? $places : $lan->places;
        $lan->price = $price != null ? $price : $lan->price;
        $lan->rules = $rules != null ? $rules : $lan->rules;
        $lan->description = $description != null ? $description : $lan->description;
        $lan->save();

        return $lan;
    }
}