<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Reservation\GetUserDetailsReservationResource;
use App\Model\Reservation;
use App\Model\User;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;

class GetUserDetailsResource extends Resource
{
    protected $currentSeat;
    protected $seatHistory;

    public function __construct(User $resource, ?Reservation $currentSeat, ?Collection $seatHistory)
    {
        $this->currentSeat = $currentSeat;
        $this->seatHistory = $seatHistory;
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'full_name' => $this->getFullName(),
            'email' => $this->email,
            'current_place' => $this->currentSeat != null ? $this->currentSeat->seat_id : null,
            'place_history' => GetUserDetailsReservationResource::collection($this->seatHistory)
        ];
    }
}
