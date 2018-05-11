<?php

namespace App\Services\Implementation;

use App\Model\Reservation;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\SeatRepositoryImpl;
use App\Repositories\SeatRepository;
use App\Services\SeatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    public function book(int $lanId, string $seatId): Reservation
    {
        $user = Auth::user();
        $lan = $this->lanRepository->findLanById($lanId);

        if ($lan == null) {
            throw new BadRequestHttpException(json_encode(
                ["lan_id" => [
                    'Lan with id ' . $lanId . ' doesn\'t exist'
                ]]
            ));
        }

        $seatsClient = new SeatsioClient($lan->secret_key_id);

        // Check if place exist in event and if it is already taken
        try{
            $status = $seatsClient->events()->retrieveObjectStatus($lan->event_key_id, $seatId);
            if($status->status === 'booked'){
                throw new BadRequestHttpException(json_encode(
                    ["seat_id" => [
                        'Seat with id ' . $seatId . ' is already taken for this event'
                    ]]
                ));
            }
        }catch (SeatsioException $exception){
            throw new BadRequestHttpException(json_encode(
                ["seat_id" => [
                    'Seat with id ' . $seatId . ' doesn\'t exist in this event'
                ]]
            ));
        }

        // validate data
        // user can only be once in a lan
        // seat can only be once in a lan
        // seat is a required string
        // lan is a required unsigned integer
        $reservationValidator = Validator::make([
            'lan_id' => $lanId,
            'seat_id' => $seatId
        ], [
            'lan_id' => 'required|integer',
            'seat_id' => 'required|string',
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        // send the place to the api
        $seatsClient->events()->book($lan->event_key_id, [$seatId]);

        // assign place to user in lan
        $this->seatRepository->attachLanUser($user, $lan, $seatId);

        // return the reservation
        return $this->seatRepository->findReservationByLanAndUserId($user->id, $lan->id);
    }
}