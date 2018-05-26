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
                'name' => $this->name,
                'lan_start' => $this->lan_start,
                'lan_end' => $this->lan_end,
                'seat_reservation_start' => $this->seat_reservation_start,
                'tournament_reservation_start' => $this->tournament_reservation_start,
                'longitude' => $this->longitude,
                'latitude' => $this->latitude,
                'price' => $this->price,
                'rules' => $this->rules,
                'description' => $this->description,
            ];
        } else {
            return [
                'id' => $this->id,
                'name' => $this->when(in_array("name", $fields), $this->name),
                'lan_start' => $this->when(in_array("lan_start", $fields), $this->lan_start),
                'lan_end' => $this->when(in_array("lan_end", $fields), $this->lan_end),
                'seat_reservation_start' => $this->when(in_array("seat_reservation_start", $fields), $this->seat_reservation_start),
                'tournament_reservation_start' => $this->when(in_array("tournament_reservation_start", $fields), $this->tournament_reservation_start),
                'longitude' => $this->when(in_array("longitude", $fields), $this->longitude),
                'latitude' => $this->when(in_array("latitude", $fields), $this->latitude),
                'price' => $this->when(in_array("price", $fields), $this->price),
                'rules' => $this->when(in_array("rules", $fields), $this->rules),
                'description' => $this->when(in_array("description", $fields), $this->description),
            ];
        }
    }
}
