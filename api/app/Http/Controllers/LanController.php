<?php

namespace App\Http\Controllers;

use App\Services\Implementation\LanServiceImpl;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;

class LanController extends Controller
{
    use Helpers;

    protected $lanService;

    /**
     * LanController constructor.
     * @param LanServiceImpl $lanServiceImpl
     */
    public function __construct(LanServiceImpl $lanServiceImpl)
    {
        $this->lanService = $lanServiceImpl;
    }

    public function createLan(Request $request)
    {
        return response()->json($this->lanService->createLan($request), 201);
    }

    public function updateLanRules(Request $request, string $lan_id)
    {
        return response()->json($this->lanService->updateRules($request, $lan_id), 201);
    }

    public function updateLanName(Request $request, string $lan_id)
    {
        return response()->json($this->lanService->updateLanName($request, $lan_id), 201);
    }

    public function updateLanPrice(Request $request, string $lan_id)
    {
        return response()->json($this->lanService->updateLanPrice($request, $lan_id), 201);
    }

    public function updateLanLocation(Request $request, string $lan_id)
    {
        return response()->json($this->lanService->updateLanLocation($request, $lan_id), 201);
    }

    public function updateLanSeatReservationStart(Request $request, string $lan_id)
    {
        return response()->json($this->lanService->updateLanSeatReservationStart($request, $lan_id), 201);
    }

    public function updateLanTournamentReservationStart(Request $request, string $lan_id)
    {
        return response()->json($this->lanService->updateLanTournamentReservationStart($request, $lan_id), 201);
    }

    public function updateLanStartDate(Request $request, string $lan_id)
    {
        return response()->json($this->lanService->updateLanStartDate($request, $lan_id), 201);
    }

    public function updateLanEndDate(Request $request, string $lan_id)
    {
        return response()->json($this->lanService->updateLanEndDate($request, $lan_id), 201);
    }

    public function updateLanSeatsKeys(Request $request, string $lan_id)
    {
        return response()->json($this->lanService->updateLanSeatsKeys($request, $lan_id), 201);
    }

    public function updateLanDescription(Request $request, string $lan_id)
    {
        return response()->json($this->lanService->updateLanDescription($request, $lan_id), 201);
    }

    public function updateLanPlaces(Request $request, string $lan_id)
    {
        return response()->json($this->lanService->updateLanPlaces($request, $lan_id), 201);
    }

    public function getLan(Request $request, string $lan_id)
    {
        return response()->json($this->lanService->getLan($request, $lan_id), 200);
    }

    public function getLans()
    {
        return response()->json($this->lanService->getLans(), 200);
    }

    public function setCurrentLan(string $lan_id)
    {
        return response()->json($this->lanService->setCurrentLan($lan_id), 200);
    }

    public function getCurrentLan()
    {
        return response()->json($this->lanService->getCurrentLan(), 200);
    }
}
