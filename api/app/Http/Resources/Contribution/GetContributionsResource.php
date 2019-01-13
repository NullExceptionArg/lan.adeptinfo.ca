<?php

namespace App\Http\Resources\Contribution;

use Illuminate\Http\Resources\Json\Resource;

class GetContributionsResource extends Resource
{
    /**
     * Transformer la ressource en tableau.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $this->resource->load('contribution');
        return [
            'category_id' => $this->id,
            'category_name' => $this->name,
            'contributions' => ContributionResource::collection($this->contribution),
        ];
    }
}
