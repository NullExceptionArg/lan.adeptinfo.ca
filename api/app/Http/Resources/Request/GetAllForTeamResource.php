<?php

namespace App\Http\Resources\Request;

use Illuminate\Http\Resources\Json\Resource;

class GetAllForTeamResource extends Resource
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
            'id' => intval($this->id),
            'tag_id' => intval($this->tag_id),
            'tag_name' => $this->tag_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name
        ];
    }
}
