<?php

namespace App\Http\Resources\Contribution;

use Illuminate\Http\Resources\Json\Resource;

class ContributionCategoryResource extends Resource
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
            'id'   => intval($this->id),
            'name' => $this->name,
        ];
    }
}
