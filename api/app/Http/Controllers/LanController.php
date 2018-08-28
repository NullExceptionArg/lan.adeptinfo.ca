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
        return response()->json($this->lanService->create($request), 201);
    }

    public function updateLan(Request $request)
    {
        return response()->json($this->lanService->edit($request), 200);
    }

    public function getLan(Request $request)
    {
        return response()->json($this->lanService->get($request), 200);
    }

    public function getAllLan()
    {
        return response()->json($this->lanService->getAll(), 200);
    }

    public function setCurrentLan(Request $request)
    {
        return response()->json($this->lanService->setCurrent($request), 200);
    }
}
