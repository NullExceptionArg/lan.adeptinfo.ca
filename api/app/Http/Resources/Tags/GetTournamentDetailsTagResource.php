<?php

namespace App\Http\Resources\Tag;

use Illuminate\Http\Resources\Json\Resource;

class GetTournamentDetailsTagResource extends Resource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'tag_id' => $this->tag_id,
            'tag_name' => $this->tag_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'is_leader' => $this->is_leader == 1 ? true : false,
            'reservation_id' => $this->reservation_id,
            'seat_id' => $this->seat_id
        ];
    }
}