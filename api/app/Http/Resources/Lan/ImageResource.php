<?php

namespace App\Http\Resources\Lan;

use Illuminate\Http\Resources\Json\Resource;

class ImageResource extends Resource
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
            'image' => $this->image
        ];
    }
}
