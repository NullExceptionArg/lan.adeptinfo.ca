<?php

namespace App\Http\Controllers;


use App\Services\Implementation\TeamServiceImpl;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    protected $teamServiceImpl;

    /**
     * LanController constructor.
     * @param TeamServiceImpl $teamServiceImpl
     */
    public function __construct(TeamServiceImpl $teamServiceImpl)
    {
        $this->teamServiceImpl = $teamServiceImpl;
    }

    public function createTeam(Request $request)
    {
        return response()->json($this->teamServiceImpl->create($request), 201);
    }

}