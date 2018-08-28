<?php

namespace App\Http\Resources\Team;


use Illuminate\Http\Resources\Json\Resource;

class GetRequestsResource extends Resource
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
            'team_id' => intval($this->team_id),
            'team_tag' => $this->team_tag,
            'team_name' => $this->team_name,
            'tournament_id' => intval($this->tournament_id),
            'tournament_name' => $this->tournament_name
        ];
    }
}