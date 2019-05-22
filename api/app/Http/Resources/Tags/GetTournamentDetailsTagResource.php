<?php

namespace App\Http\Resources\Tag;

use Illuminate\Http\Resources\Json\Resource;

class GetTournamentDetailsTagResource extends Resource
{
    /**
     * Transformer la ressource en tableau.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'tag_id'         => intval($this->tag_id),
            'tag_name'       => $this->tag_name,
            'first_name'     => $this->first_name,
            'last_name'      => $this->last_name,
            'is_leader'      => $this->is_leader == 1 ? true : false,
            'reservation_id' => !is_null($this->reservation_id) ? intval($this->reservation_id) : null,
            'seat_id'        => $this->seat_id,
        ];
    }
}
