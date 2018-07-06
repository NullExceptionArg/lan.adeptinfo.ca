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

    public function updateLan(Request $request, string $lanId)
    {
        return response()->json($this->lanService->update($request, $lanId), 200);
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