<?php

namespace App\Http\Resources\Contribution;

use Illuminate\Http\Resources\Json\Resource;

class GetContributionsResource extends Resource
{
    /**
     * Transform the resource into an array.
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
