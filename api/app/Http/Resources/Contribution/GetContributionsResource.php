<?php

namespace App\Http\Resources\Contribution;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;

class GetContributionsResource extends Resource
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
        $contributions = DB::table('contribution')
            ->where('contribution_category_id', $this->id)
            ->get();

        return [
            'category_id'   => $this->id,
            'category_name' => $this->name,
            'contributions' => ContributionResource::collection($contributions),
        ];
    }
}
