<?php

namespace App\Http\Resources\Team;

use App\Http\Resources\{Request\GetAllForTeamResource, Tag\GetUserTagsResource};
use App\Model\Team;
use Illuminate\{Http\Resources\Json\Resource, Support\Collection};

class GetUsersTeamDetailsResource extends Resource
{
    protected $tags;
    protected $requests;

    public function __construct(Team $resource, Collection $tags, ?Collection $requests)
    {
        $this->tags = $tags;
        $this->requests = $requests;
        parent::__construct($resource);
    }

    /**
     * Transformer la ressource en tableau.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => intval($this->id),
            'name' => $this->name,
            'team_tag' => $this->tag,
            'user_tags' => GetUserTagsResource::collection($this->tags),
            'requests' => $this->when(
                !is_null($this->requests),
                !is_null($this->requests) ? GetAllForTeamResource::collection($this->requests) : null
            )
        ];
    }
}
