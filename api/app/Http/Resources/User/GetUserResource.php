<?php

namespace App\Http\Resources\User;

use App\Model\Lan;
use App\Model\Lan as LanResource;
use App\Model\Reservation;
use Illuminate\Http\Resources\Json\Resource;

class GetUserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $lans = Reservation::where('user_id', $this->id);
        return [
            'full_name' => $this->getFullName(),
            'email' => $this->email,
            'reservations' => LanResource::collection($this->lan())
        ];
    }
}
