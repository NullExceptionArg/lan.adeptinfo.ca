<?php

namespace App\Services\Implementation;

use App\Model\Reservation;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\SeatRepositoryImpl;
use App\Services\SeatService;
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

    public function book(string $lanId, string $seatId): Reservation
    {
        // Internal validation

        $reservationValidator = Validator::make([
            'lan_id' => $lanId,
            'seat_id' => $seatId
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'seat_id' => 'required|string',
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        $user = Auth::user();
        $lan = $this->lanRepository->findLanById($lanId);

        $seatsClient = new SeatsioClient($lan->secret_key_id);

        // GetUserResource can only have one seat in a lan
        $lanUserReservation = $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);
        if ($lanUserReservation != null && $lanUserReservation->count() > 0) {
            throw new BadRequestHttpException(json_encode([
                "lan_id" => [
                    'The user already has a seat at this event'
                ]
            ]));
        }

        // Seat can only be once in a lan
        $lanSeatReservation = $this->seatRepository->findReservationByLanIdAndSeatId($lan->id, $seatId);
        if ($lanSeatReservation != null && $lanSeatReservation->count() > 0) {
            throw new BadRequestHttpException(json_encode([
                "seat_id" => [
                    'Seat with id ' . $seatId . ' is already taken for this event'
                ]
            ]));
        }

        // Seats.io validation

        // Check if place exist in the event and if it is already taken
        try {
            $status = $seatsClient->events()->retrieveObjectStatus($lan->event_key_id, $seatId);
            if ($status->status === 'booked' || $status->status === 'arrived') {
                throw new BadRequestHttpException(json_encode([
                    "seat_id" => [
                        'Seat with id ' . $seatId . ' is already taken for this event'
                    ]
                ]));
            }
        } catch (SeatsioException $exception) {
            throw new BadRequestHttpException(json_encode([
                "seat_id" => [
                    'Seat with id ' . $seatId . ' doesn\'t exist in this event'
                ]
            ]));
        }


        // send the place to the api
        $seatsClient->events()->book($lan->event_key_id, [$seatId]);

        // create reservation
        $this->seatRepository->createReservation($user->id, $lan->id, $seatId);

        // return the reservation
        return $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);
    }

    public function confirmArrival(string $lanId, string $seatId): Reservation
    {
        // Internal validation

        $reservationValidator = Validator::make([
            'lan_id' => $lanId,
            'seat_id' => $seatId
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'seat_id' => 'required|string|exists:reservation,seat_id',
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        // Seats.io validation
        $seatsClient = new SeatsioClient($lan->secret_key_id);

        // Check if place exist in the event and if it is already taken
        try {
            $status = $seatsClient->events()->retrieveObjectStatus($lan->event_key_id, $seatId);
            switch ($status->status) {
                case 'free':
                    throw new BadRequestHttpException(json_encode([
                        "seat_id" => [
                            'Seat with id ' . $seatId . ' is not associated with a reservation'
                        ]
                    ]));
                    break;
                case 'arrived':
                    throw new BadRequestHttpException(json_encode([
                        "seat_id" => [
                            "Seat with id " . $seatId . " is already set to 'arrived'"
                        ]
                    ]));
            }
        } catch (SeatsioException $exception) {
            throw new BadRequestHttpException(json_encode([
                "seat_id" => [
                    'Seat with id ' . $seatId . ' doesn\'t exist in this event'
                ]
            ]));
        }

        $seatsClient->events()->changeObjectStatus($lan->event_key_id, [$seatId], "arrived");

        $reservation = $this->seatRepository->findReservationByLanIdAndSeatId($lan->id, $seatId);

        return $reservation;
    }

    public function unConfirmArrival(string $lanId, string $seatId): Reservation
    {
        // Internal validation

        $reservationValidator = Validator::make([
            'lan_id' => $lanId,
            'seat_id' => $seatId
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'seat_id' => 'required|string|exists:reservation,seat_id',
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        // Seats.io validation
        $seatsClient = new SeatsioClient($lan->secret_key_id);

        // Check if place exist in the event and if it is already taken
        try {
            $status = $seatsClient->events()->retrieveObjectStatus($lan->event_key_id, $seatId);
            switch ($status->status) {
                case 'free':
                    throw new BadRequestHttpException(json_encode([
                        "seat_id" => [
                            'Seat with id ' . $seatId . ' is not associated with a reservation'
                        ]
                    ]));
                    break;
                case 'booked':
                    throw new BadRequestHttpException(json_encode([
                        "seat_id" => [
                            "Seat with id " . $seatId . " is already set to 'booked'"
                        ]
                    ]));
            }
        } catch (SeatsioException $exception) {
            throw new BadRequestHttpException(json_encode([
                "seat_id" => [
                    'Seat with id ' . $seatId . ' doesn\'t exist in this event'
                ]
            ]));
        }

        $seatsClient->events()->changeObjectStatus($lan->event_key_id, [$seatId], "booked");

        $reservation = $this->seatRepository->findReservationByLanIdAndSeatId($lan->id, $seatId);

        return $reservation;
    }
}