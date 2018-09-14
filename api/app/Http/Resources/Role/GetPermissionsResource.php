<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Resources\Json\Resource;

class GetPermissionsResource extends Resource
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
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => trans('permission.display-name-' . $this->name),
            'description' => trans('permission.description-' . $this->name)
        ];
    }
}
