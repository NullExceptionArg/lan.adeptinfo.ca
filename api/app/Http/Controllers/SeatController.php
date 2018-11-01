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

    public function bookSeat(Request $request, string $seatId)
    {
        return response()->json($this->seatService->book($request, $seatId), 201);
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

    public function unBookSeat(Request $request, string $seatId)
    {
        return response()->json($this->seatService->unBook($request, $seatId), 200);
    }

    public function unAssignSeat(Request $request, string $seatId)
    {
        return response()->json($this->seatService->unAssign($request, $seatId), 200);
    }
}
