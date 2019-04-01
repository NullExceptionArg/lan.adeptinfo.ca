<?php

namespace App\Http\Resources\Lan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class GetAllResource extends Resource
{
    /**
     * Transformer la ressource en tableau.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_current' => $this->is_current,
            'date' => $this->getDateAttribute()
        ];
    }
}
