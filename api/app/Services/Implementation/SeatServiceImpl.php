<?php

namespace App\Services\Implementation;

use App\Model\Reservation;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\SeatRepositoryImpl;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Services\SeatService;
use Illuminate\Support\Facades\Auth;
use Seatsio\SeatsioClient;

class SeatServiceImpl implements SeatService
{
    protected $lanRepository;
    protected $seatRepository;
    protected $userRepository;

    /**
     * LanServiceImpl constructor.
     * @param LanRepositoryImpl $lanRepositoryImpl
     * @param SeatRepositoryImpl $seatRepositoryImpl
     * @param UserRepositoryImpl $userRepositoryImpl
     */
    public function __construct(
        LanRepositoryImpl $lanRepositoryImpl,
        SeatRepositoryImpl $seatRepositoryImpl,
        UserRepositoryImpl $userRepositoryImpl
    )
    {
        $this->lanRepository = $lanRepositoryImpl;
        $this->seatRepository = $seatRepositoryImpl;
        $this->userRepository = $userRepositoryImpl;
    }

    public function assign(int $lanId, string $email, string $seatId): Reservation
    {
        $user = $this->userRepository->findByEmail($email);
        $lan = $this->lanRepository->findById($lanId);
        $seatsClient = new SeatsioClient($lan->secret_key);
        $seatObject = [[
            'objectId' => $seatId,
            'extraData' => [
                'name' => $user->getFullName(),
                'email' => $user->email
            ]
        ]];

        $seatsClient->events->book($lan->event_key, $seatObject);
        $this->seatRepository->createReservation($user->id, $lan->id, $seatId);

        return $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);
    }

    public function book(int $lanId, string $seatId): Reservation
    {
        $user = Auth::user();
        $lan = $this->lanRepository->findById($lanId);
        $seatsClient = new SeatsioClient($lan->secret_key);
        $seatObject = [[
            'objectId' => $seatId,
            'extraData' => [
                'name' => $user->getFullName(),
                'email' => $user->email]
        ]];

        $seatsClient->events->book($lan->event_key, $seatObject);
        $this->seatRepository->createReservation($user->id, $lan->id, $seatId);

        return $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);
    }

    public function confirmArrival(int $lanId, string $seatId): Reservation
    {
        $lan = $this->lanRepository->findById($lanId);
        $seatsClient = new SeatsioClient($lan->secret_key);
        $reservation = $this->seatRepository->findReservationByLanIdAndSeatId($lan->id, $seatId);

        $seatsClient->events->changeObjectStatus($lan->event_key, [$seatId], "arrived");
        $this->seatRepository->setReservationArrived($reservation->id, $lan->id);

        return $reservation;
    }

    public function unAssign(int $lanId, string $email, string $seatId): Reservation
    {
        $user = $this->userRepository->findByEmail($email);
        $lan = $this->lanRepository->findById($lanId);
        $seatsClient = new SeatsioClient($lan->secret_key);
        $reservation = $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);

        $seatsClient->events->release($lan->event_key, [$seatId]);
        $this->seatRepository->deleteReservation($reservation->id);

        return $reservation;
    }

    public function unBook(int $lanId, string $seatId): Reservation
    {
        $user = Auth::user();
        $lan = $this->lanRepository->findById($lanId);
        $seatsClient = new SeatsioClient($lan->secret_key);
        $reservation = $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);

        $seatsClient->events->release($lan->event_key, [$seatId]);
        $this->seatRepository->deleteReservation($reservation->id);

        return $reservation;
    }

    public function unConfirmArrival(int $lanId, string $seatId): Reservation
    {
        $lan = $this->lanRepository->findById($lanId);
        $seatsClient = new SeatsioClient($lan->secret_key);
        $reservation = $this->seatRepository->findReservationByLanIdAndSeatId($lan->id, $seatId);

        $seatsClient->events->changeObjectStatus($lan->event_key, [$seatId], "booked");
        $this->seatRepository->setReservationLeft($reservation->id, $lan->id);

        return $reservation;
    }
}
