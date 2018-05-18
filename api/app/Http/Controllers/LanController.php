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

    public function createLan(Request $request){
        return response()->json($this->lanService->createLan($request), 201);
    }

    public function updateLanRules(Request $request, string $lan_id)
    {
        return response()->json($this->lanService->updateRules($request, $lan_id), 201);
    }
}
