<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Role\GetPermissionsResource;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;

class GetAdminSummaryResource extends Resource
{
    protected $permissions;

    public function __construct(Authenticatable $resource, Collection $permissions)
    {
        $this->permissions = $permissions;
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
            'permissions' => GetPermissionsResource::collection($this->permissions)
        ];
    }
}
