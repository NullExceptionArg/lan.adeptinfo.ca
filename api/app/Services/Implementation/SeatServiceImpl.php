<?php

namespace App\Services\Implementation;

use App\Model\Reservation;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\SeatRepositoryImpl;
use App\Repositories\SeatRepository;
use App\Services\SeatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Seatsio\SeatsioClient;

class SeatServiceImpl implements SeatService
{
    protected $lanRepository;
    protected $seatRepository;

    /**
     * LanServiceImpl constructor.
     * @param LanRepositoryImpl $lanRepositoryImpl
     * @param SeatRepositoryImpl $seatRepositoryImpl
     */
    public function __construct(LanRepositoryImpl $lanRepositoryImpl, SeatRepositoryImpl $seatRepositoryImpl)
    {
        $this->lanRepository = $lanRepositoryImpl;
        $this->seatRepository = $seatRepositoryImpl;
    }

    public function book(Request $input): Reservation
    {
        $user = Auth::user();
        $lan = $this->lanRepository->findById($input['lan_id']);


        // validate data
        // user can only be once in a lan
        // seat can only be once in a lan
        // seat is a required string
        // lan is a required unsigned integer
        //

        // send the place to the api
        $seatsClient = new SeatsioClient($lan->secret_key_id);
//        $seatsClient->events()->release($lan->event_key_id, [$input['seat_id']]);
//        $seatsClient->events()->book($lan->event_key_id, [$input['seat_id']]);

        // assign place to user in lan
        $this->seatRepository->attachLanUser($user, $lan, $input['seat_id']);

        // return the reservation
        return $this->seatRepository->findReservationByLanAndUserId($user->id, $lan->id);
    }
}