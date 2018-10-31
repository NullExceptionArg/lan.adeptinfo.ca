<?php

namespace App\Services\Implementation;

use App\Model\Reservation;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\SeatRepositoryImpl;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Rules\HasPermission;
use App\Rules\SeatExistInLanSeatIo;
use App\Rules\SeatLanRelationExists;
use App\Rules\SeatNotArrivedSeatIo;
use App\Rules\SeatNotBookedSeatIo;
use App\Rules\SeatNotFreeSeatIo;
use App\Rules\SeatOncePerLan;
use App\Rules\SeatOncePerLanSeatIo;
use App\Rules\UserOncePerLan;
use App\Services\SeatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Seatsio\SeatsioClient;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    public function book(Request $input, string $seatId): Reservation
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $reservationValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'seat_id' => $seatId
        ], [
            'lan_id' => [
                'required',
                'integer',
                'exists:lan,id,deleted_at,NULL',
                new UserOncePerLan(Auth::user(), null)
            ],
            'seat_id' => [
                'required',
                'string',
                new SeatOncePerLan($input->input('lan_id')),
                new SeatOncePerLanSeatIo($input->input('lan_id')),
                new SeatExistInLanSeatIo($input->input('lan_id'))
            ],
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        $user = Auth::user();
        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        $seatsClient = new SeatsioClient($lan->secret_key);
        $seatObject = [
            ['objectId' => $seatId, 'extraData' => ['name' => $user->getFullName(), 'email' => $user->email]]
        ];
        $seatsClient->events()->book($lan->event_key, $seatObject);
        $this->seatRepository->createReservation($user->id, $lan->id, $seatId);

        return $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);
    }

    public function confirmArrival(Request $input, string $seatId): Reservation
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $reservationValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'seat_id' => $seatId,
            'permission' => 'confirm-arrival'
        ], [
            'lan_id' => 'required|integer|exists:lan,id,deleted_at,NULL',
            'seat_id' => [
                'required',
                'string',
                new SeatExistInLanSeatIo($input->input('lan_id')),
                new SeatNotFreeSeatIo($input->input('lan_id')),
                new SeatNotArrivedSeatIo($input->input('lan_id')),
                new SeatLanRelationExists($input->input('lan_id'))
            ],
            'permission' => new HasPermission($input->input('lan_id'), Auth::id())
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        $seatsClient = new SeatsioClient($lan->secret_key);
        $seatsClient->events()->changeObjectStatus($lan->event_key, [$seatId], "arrived");
        $reservation = $this->seatRepository->findReservationByLanIdAndSeatId($lan->id, $seatId);
        $this->seatRepository->setReservationArrived($reservation);

        return $reservation;
    }

    public function unConfirmArrival(Request $input, string $seatId): Reservation
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $reservationValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'seat_id' => $seatId,
            'permission' => 'unconfirm-arrival',
        ], [
            'lan_id' => 'required|integer|exists:lan,id,deleted_at,NULL',
            'seat_id' => [
                'required',
                'string',
                'exists:reservation,seat_id,deleted_at,NULL',
                new SeatNotFreeSeatIo($input->input('lan_id')),
                new SeatNotBookedSeatIo($input->input('lan_id')),
                new SeatExistInLanSeatIo($input->input('lan_id')),
                new SeatLanRelationExists($input->input('lan_id'))
            ],
            'permission' => new HasPermission($input->input('lan_id'), Auth::id())
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        $seatsClient = new SeatsioClient($lan->secret_key);
        $seatsClient->events()->changeObjectStatus($lan->event_key, [$seatId], "booked");
        $reservation = $this->seatRepository->findReservationByLanIdAndSeatId($lan->id, $seatId);
        $this->seatRepository->setReservationLeft($reservation);

        return $reservation;
    }

    public function assign(Request $input, string $seatId): Reservation
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $reservationValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'seat_id' => $seatId,
            'user_email' => $input->input('user_email')
        ], [
            'user_email' => 'exists:user,email',
            'lan_id' => [
                'integer',
                'exists:lan,id,deleted_at,NULL',
                new UserOncePerLan(null, $input->input('user_email'))
            ],
            'seat_id' => [
                'required',
                'string',
                new SeatOncePerLan($input->input('lan_id')),
                new SeatOncePerLanSeatIo($input->input('lan_id')),
                new SeatExistInLanSeatIo($input->input('lan_id'))
            ]
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        $user = $this->userRepository->findByEmail($input->input('user_email'));
        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        $seatsClient = new SeatsioClient($lan->secret_key);
        $seatObject = [
            ['objectId' => $seatId, 'extraData' => ['name' => $user->getFullName(), 'email' => $user->email]]
        ];
        $seatsClient->events()->book($lan->event_key, $seatObject);
        $this->seatRepository->createReservation($user->id, $lan->id, $seatId);

        return $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);
    }

    public function unBook(Request $input, string $seatId): Reservation
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $reservationValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'seat_id' => $seatId
        ], [
            'lan_id' => [
                'required',
                'integer',
                'exists:lan,id,deleted_at,NULL'
            ],
            'seat_id' => [
                'required',
                'string',
                new SeatExistInLanSeatIo($input->input('lan_id')),
                new SeatLanRelationExists($input->input('lan_id'))
            ],
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        $user = Auth::user();
        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        $seatsClient = new SeatsioClient($lan->secret_key);
        $seatsClient->events()->release($lan->event_key, [$seatId]);
        $reservation = $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);
        $this->seatRepository->deleteReservation($reservation);

        return $reservation;
    }

    public function unAssign(Request $input, string $seatId): Reservation
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $reservationValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'user_email' => $input->input('user_email'),
            'seat_id' => $seatId
        ], [
            'user_email' => 'exists:user,email',
            'lan_id' => [
                'required',
                'integer',
                'exists:lan,id,deleted_at,NULL'
            ],
            'seat_id' => [
                'required',
                'string',
                new SeatExistInLanSeatIo($input->input('lan_id')),
                new SeatLanRelationExists($input->input('lan_id'))
            ],
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        $user = $this->userRepository->findByEmail($input->input('user_email'));
        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        $seatsClient = new SeatsioClient($lan->secret_key);
        $seatsClient->events()->release($lan->event_key, [$seatId]);
        $reservation = $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);
        $this->seatRepository->deleteReservation($reservation);

        return $reservation;
    }
}