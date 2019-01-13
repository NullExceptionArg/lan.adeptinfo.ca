<?php

namespace App\Repositories\Implementation;

use App\Model\{Lan, LanImage};
use App\Repositories\LanRepository;
use DateTime;
use Illuminate\{Support\Collection, Support\Facades\DB};

class LanRepositoryImpl implements LanRepository
{
    public function addLanImage(int $lanId, string $image): int
    {
        return DB::table('image')
            ->insertGetId([
                'lan_id' => $lanId,
                'image' => $image
            ]);
    }

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
        bool $isCurrent,
        ?int $price,
        ?string $rules,
        ?string $description
    ): int
    {
        return DB::table('lan')
            ->insertGetId([
                'name' => $name,
                'lan_start' => $lanStart->format('Y-m-d H:i:s'),
                'lan_end' => $lanEnd->format('Y-m-d H:i:s'),
                'seat_reservation_start' => $seatReservationStart->format('Y-m-d H:i:s'),
                'tournament_reservation_start' => $tournamentReservationStart->format('Y-m-d H:i:s'),
                'event_key' => $eventKey,
                'public_key' => $publicKey,
                'secret_key' => $secretKey,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'places' => $places,
                'is_current' => $isCurrent,
                'price' => $price,
                'rules' => $rules,
                'description' => $description,
            ]);
    }

    public function deleteLanImages(array $imageIds): void
    {
        LanImage::destroy($imageIds);
    }

    public function findById(int $id): ?Lan
    {
        return Lan::find($id);
    }

    public function findLanImageById(int $imageId): ?LanImage
    {
        return LanImage::find($imageId);
    }

    public function getAll(): ?Collection
    {
        return Lan::all();
    }

    public function getImagesForLan(int $lanId): Collection
    {
        return DB::table('image')
            ->where('lan_id', $lanId)
            ->get();
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
    ): void
    {
        $lan = $this->findById($lanId);
        DB::table('lan')
            ->where('id', $lanId)
            ->update([
                'name' => $name != null ? $name : $lan->name,
                'lan_start' => $lanStart != null ? $lanStart->format('Y-m-d H:i:s') : $lan->lan_start->format('Y-m-d H:i:s'),
                'lan_end' => $lanEnd != null ? $lanEnd->format('Y-m-d H:i:s') : $lan->lan_end->format('Y-m-d H:i:s'),
                'seat_reservation_start' => $seatReservationStart != null ? $seatReservationStart->format('Y-m-d H:i:s') : $lan->seat_reservation_start->format('Y-m-d H:i:s'),
                'tournament_reservation_start' => $tournamentReservationStart != null ? $tournamentReservationStart->format('Y-m-d H:i:s') : $lan->tournament_reservation_start->format('Y-m-d H:i:s'),
                'event_key' => $eventKey != null ? $eventKey : $lan->event_key,
                'public_key' => $publicKey != null ? $publicKey : $lan->public_key,
                'secret_key' => $secretKey != null ? $secretKey : $lan->secret_key,
                'latitude' => $latitude != null ? $latitude : $lan->latitude,
                'longitude' => $longitude != null ? $longitude : $lan->longitude,
                'places' => $places != null ? $places : $lan->places,
                'price' => $price != null ? $price : $lan->price,
                'rules' => $rules != null ? $rules : $lan->rules,
                'description' => $description != null ? $description : $lan->description,
            ]);
    }
}
