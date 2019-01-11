<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Role\GetPermissionsResource;
use Illuminate\{Contracts\Auth\Authenticatable, Http\Resources\Json\Resource, Support\Collection};

class GetAdminSummaryResource extends Resource
{
    protected $permissions;
    protected $hasTournaments;

    public function __construct(Authenticatable $resource, bool $hasTournaments, Collection $permissions)
    {
        $this->permissions = $permissions;
        $this->hasTournaments = $hasTournaments;
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'has_tournaments' => $this->hasTournaments,
            'permissions' => GetPermissionsResource::collection($this->permissions)
        ];
    }
}
