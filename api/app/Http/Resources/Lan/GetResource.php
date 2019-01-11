<?php

namespace App\Http\Resources\Lan;

use App\Model\Lan;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed lan_start
 * @property mixed lan_end
 * @property mixed seat_reservation_start
 * @property mixed tournament_reservation_start
 * @property mixed secret_key
 * @property mixed event_key
 * @property mixed public_key
 * @property mixed places
 * @property mixed longitude
 * @property mixed latitude
 * @property mixed price
 * @property mixed rules
 * @property mixed description
 */
class GetResource extends Resource
{
    protected $reservedPlaces;
    protected $images;
    protected $fields;

    public function __construct(Lan $resource, int $reservedPlaces, Collection $images, ?string $fields)
    {
        $this->reservedPlaces = $reservedPlaces;
        $this->images = $images;
        $this->fields = $fields;
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
        $fields = explode(',', $this->fields);
        if (substr_count($this->fields, ',') == 0) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'lan_start' => $this->lan_start,
                'lan_end' => $this->lan_end,
                'seat_reservation_start' => $this->seat_reservation_start,
                'tournament_reservation_start' => $this->tournament_reservation_start,
                'longitude' => floatval(number_format($this->longitude, 7)),
                'latitude' => floatval(number_format($this->latitude, 7)),
                'secret_key' => $this->secret_key,
                'event_key' => $this->event_key,
                'public_key' => $this->public_key,
                'places' => [
                    'reserved' => $this->reservedPlaces,
                    'total' => $this->places
                ],
                'price' => $this->price,
                'rules' => $this->rules,
                'description' => $this->description,
                'images' => $this->images
            ];
        } else {
            return [
                'id' => $this->id,
                'name' => $this->when(in_array("name", $fields), $this->name),
                'lan_start' => $this->when(in_array("lan_start", $fields), $this->lan_start),
                'lan_end' => $this->when(in_array("lan_end", $fields), $this->lan_end),
                'seat_reservation_start' => $this->when(in_array("seat_reservation_start", $fields), $this->seat_reservation_start),
                'tournament_reservation_start' => $this->when(in_array("tournament_reservation_start", $fields), $this->tournament_reservation_start),
                'longitude' => $this->when(in_array("longitude", $fields), floatval(number_format($this->longitude, 7))),
                'latitude' => $this->when(in_array("latitude", $fields), floatval(number_format($this->latitude, 7))),
                'secret_key' => $this->when(in_array("secret_key", $fields), $this->secret_key),
                'event_key' => $this->when(in_array("event_key", $fields), $this->event_key),
                'public_key' => $this->when(in_array("public_key", $fields), $this->public_key),
                "places" => $this->when(in_array("places", $fields), [
                    "reserved" => $this->reservedPlaces,
                    "total" => $this->places,
                ]),
                'price' => $this->when(in_array("price", $fields), $this->price),
                'rules' => $this->when(in_array("rules", $fields), $this->rules),
                'description' => $this->when(in_array("description", $fields), $this->description),
                'images' => $this->when(in_array("images", $fields), $this->images)
            ];
        }
    }
}
