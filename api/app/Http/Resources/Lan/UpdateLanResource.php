<?php

namespace App\Http\Resources\Lan;

use App\Model\Lan;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;

class UpdateLanResource extends Resource
{
    protected $reservedPlaces;
    protected $images;

    public function __construct(Lan $resource, int $reservedPlaces, Collection $images)
    {
        $this->reservedPlaces = $reservedPlaces;
        $this->images = $images;
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lan_start' => $this->lan_start,
            'lan_end' => $this->lan_end,
            'seat_reservation_start' => $this->seat_reservation_start,
            'tournament_reservation_start' => $this->tournament_reservation_start,
            'longitude' => floatval(number_format($this->longitude, 7)),
            'latitude' => floatval(number_format($this->latitude, 7)),
            'secret_key_id' => $this->secret_key_id,
            'event_key_id' => $this->event_key_id,
            'public_key_id' => $this->public_key_id,
            'places' => [
                'reserved' => $this->reservedPlaces,
                'total' => $this->places
            ],
            'price' => $this->price,
            'rules' => $this->rules,
            'description' => $this->description,
            'images' => $this->images
        ];

    }
}
