<?php

namespace App\Http\Controllers;

use App\Services\Implementation\SeatServiceImpl;
use App\Services\SeatService;
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

    public function book(Request $request)
    {
        return response()->json($this->seatService->book($request), 201);
    }
}
