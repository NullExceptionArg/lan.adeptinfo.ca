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

    public function bookSeat(string $lan_id, string $seat_id)
    {
        return response()->json($this->seatService->book($lan_id, $seat_id), 201);
    }

    public function confirmArrival(string $lanId, string $seatId)
    {
        return response()->json($this->seatService->confirmArrival($lanId, $seatId), 200);
    }

    public function unConfirmArrival(string $lanId, string $seatId)
    {
        return response()->json($this->seatService->unConfirmArrival($lanId, $seatId), 200);
    }

    public function assignSeat(Request $request)
    {
        return response()->json($this->seatService->assign($request), 201);
    }
}
