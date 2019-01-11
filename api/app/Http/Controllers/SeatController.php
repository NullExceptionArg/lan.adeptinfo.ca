<?php

namespace App\Http\Controllers;

use App\Rules\{Seat\SeatExistInLanSeatIo,
    Seat\SeatLanRelationExists,
    Seat\SeatNotArrivedSeatIo,
    Seat\SeatNotBookedSeatIo,
    Seat\SeatNotFreeSeatIo,
    Seat\SeatOncePerLan,
    Seat\SeatOncePerLanSeatIo,
    Seat\UserOncePerLan,
    User\HasPermissionInLan};
use App\Services\Implementation\SeatServiceImpl;
use Illuminate\{Http\Request, Support\Facades\Auth, Support\Facades\Validator};

class SeatController extends Controller
{
    protected $seatService;

    /**
     * SeatController constructor.
     * @param SeatServiceImpl $seatServiceImpl
     */
    public function __construct(SeatServiceImpl $seatServiceImpl)
    {
        $this->seatService = $seatServiceImpl;
    }

    public function assign(Request $request, string $seatId)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'seat_id' => $seatId,
            'user_email' => $request->input('user_email'),
            'permission' => 'assign-seat',
        ], [
            'user_email' => 'exists:user,email',
            'lan_id' => [
                'integer',
                'exists:lan,id,deleted_at,NULL',
                new UserOncePerLan(null, $request->input('user_email'))
            ],
            'seat_id' => [
                'required',
                'string',
                new SeatOncePerLan($request->input('lan_id')),
                new SeatOncePerLanSeatIo($request->input('lan_id')),
                new SeatExistInLanSeatIo($request->input('lan_id'))
            ],
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->seatService->assign(
            $request->input('lan_id'),
            $request->input('user_email'),
            $seatId
        ), 201);
    }

    public function book(Request $request, string $seatId)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
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
                new SeatOncePerLan($request->input('lan_id')),
                new SeatOncePerLanSeatIo($request->input('lan_id')),
                new SeatExistInLanSeatIo($request->input('lan_id'))
            ],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->seatService->book(
            $request->input('lan_id'),
            $seatId
        ), 201);
    }

    public function confirmArrival(Request $request, string $seatId)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'seat_id' => $seatId,
            'permission' => 'confirm-arrival'
        ], [
            'lan_id' => 'required|integer|exists:lan,id,deleted_at,NULL',
            'seat_id' => [
                'required',
                'string',
                new SeatExistInLanSeatIo($request->input('lan_id')),
                new SeatNotFreeSeatIo($request->input('lan_id')),
                new SeatNotArrivedSeatIo($request->input('lan_id')),
                new SeatLanRelationExists($request->input('lan_id'))
            ],
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->seatService->confirmArrival(
            $request->input('lan_id'),
            $seatId
        ), 200);
    }

    public function unAssign(Request $request, string $seatId)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'user_email' => $request->input('user_email'),
            'seat_id' => $seatId,
            'permission' => 'unassign-seat',
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
                new SeatExistInLanSeatIo($request->input('lan_id')),
                new SeatLanRelationExists($request->input('lan_id'))
            ],
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->seatService->unAssign(
            $request->input('lan_id'),
            $request->input('user_email'),
            $seatId
        ), 200);
    }

    public function unBook(Request $request, string $seatId)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
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
                new SeatExistInLanSeatIo($request->input('lan_id')),
                new SeatLanRelationExists($request->input('lan_id'))
            ],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->seatService->unBook(
            $request->input('lan_id'),
            $seatId
        ), 200);
    }

    public function unConfirmArrival(Request $request, string $seatId)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'seat_id' => $seatId,
            'permission' => 'unconfirm-arrival',
        ], [
            'lan_id' => 'required|integer|exists:lan,id,deleted_at,NULL',
            'seat_id' => [
                'required',
                'string',
                'exists:reservation,seat_id,deleted_at,NULL',
                new SeatNotFreeSeatIo($request->input('lan_id')),
                new SeatNotBookedSeatIo($request->input('lan_id')),
                new SeatExistInLanSeatIo($request->input('lan_id')),
                new SeatLanRelationExists($request->input('lan_id'))
            ],
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->seatService->unConfirmArrival(
            $request->input('lan_id'),
            $seatId
        ), 200);
    }
}
