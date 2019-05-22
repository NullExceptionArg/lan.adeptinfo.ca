<?php

namespace App\Http\Resources\Lan;

use App\Utils\DateUtils;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class GetAllResource extends Resource
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
        $date = Carbon::parse($this->lan_start);

        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'is_current' => $this->is_current,
            'date'       => DateUtils::getLocalizedMonth($date->month, app('translator')->getLocale()).
                ' '.$date->year,
        ];
    }
}
