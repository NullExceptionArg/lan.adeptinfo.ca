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

    public function createRequest(Request $request)
    {
        return response()->json($this->teamServiceImpl->createRequest($request), 201);
    }

    public function getUserTeams(Request $request)
    {
        return response()->json($this->teamServiceImpl->getUserTeams($request), 200);
    }

    public function getUsersTeamDetails(Request $request)
    {
        return response()->json($this->teamServiceImpl->getUsersTeamDetails($request), 200);
    }

    public function changeLeader(Request $request)
    {
        return response()->json($this->teamServiceImpl->changeLeader($request), 200);
    }

    public function acceptRequest(Request $request)
    {
        return response()->json($this->teamServiceImpl->acceptRequest($request), 200);
    }

    public function getRequests(Request $request)
    {
        return response()->json($this->teamServiceImpl->getRequests($request), 200);
    }

}