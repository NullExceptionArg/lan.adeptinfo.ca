<?php

namespace App\Http\Controllers;

use App\Services\Implementation\SeatServiceImpl;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    use Helpers;

    protected $seatService;

    /**
     * LanController constructor.
     * @param SeatServiceImpl $seatServiceImpl
     */
    public function __construct(SeatServiceImpl $seatServiceImpl)
    {
        $this->seatService = $seatServiceImpl;
    }

    public function bookSeat(Request $request, string $seat_id)
    {
        return response()->json($this->seatService->book($request, $seat_id), 201);
    }

    public function confirmArrival(Request $request, string $seatId)
    {
        return response()->json($this->seatService->confirmArrival($request, $seatId), 200);
    }

    public function unConfirmArrival(Request $request, string $seatId)
    {
        return response()->json($this->seatService->unConfirmArrival($request, $seatId), 200);
    }

    public function assignSeat(Request $request, string $seatId)
    {
        return response()->json($this->seatService->assign($request, $seatId), 201);
    }

    public function unbookSeat(Request $request, string $seatId)
    {
        return response()->json($this->seatService->unbook($request, $seatId), 200);
    }

    public function cancelSeat(Request $request, string $seatId)
    {
        return response()->json($this->seatService->cancel($request, $seatId), 201);
    }
}
