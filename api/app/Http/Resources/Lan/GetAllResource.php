<?php

namespace App\Http\Resources\Lan;

use Illuminate\Http\Resources\Json\Resource;

class GetAllResource extends Resource
{
    /**
     * Transformer la ressource en tableau.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => date('F Y', strtotime($this->lan_start))
        ];
    }
}
