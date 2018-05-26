<?php

namespace App\Http\Resources\Lan;

use Illuminate\Http\Resources\Json\Resource;

class LanResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $fields = explode(',', $request->input('fields'));
        if (substr_count($request->input('fields'), ',') == 0) {
            return [
                'id' => $this->id,
                'lan_start' => $this->lan_start,
                'lan_end' => $this->lan_end,
                'seat_reservation_start' => $this->seat_reservation_start,
                'tournament_reservation_start' => $this->tournament_reservation_start,
                'places' => [
                    'reserved' => $this->reserved_places,
                    'total' => $this->places
                ],
                'price' => $this->price,
                'rules' => $this->rules,
            ];
        } else {
            return [
                'id' => $this->id,
                'lan_start' => $this->when(in_array("lan_start", $fields), $this->lan_start),
                'lan_end' => $this->when(in_array("lan_end", $fields), $this->lan_end),
                'seat_reservation_start' => $this->when(in_array("seat_reservation_start", $fields), $this->seat_reservation_start),
                'tournament_reservation_start' => $this->when(in_array("tournament_reservation_start", $fields), $this->tournament_reservation_start),
                'price' => $this->when(in_array("price", $fields), $this->price),
                'rules' => $this->when(in_array("rules", $fields), $this->rules),
            ];
        }
    }
}
