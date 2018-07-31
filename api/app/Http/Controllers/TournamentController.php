<?php

namespace App\Http\Controllers;


use App\Services\Implementation\TournamentServiceImpl;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    protected $tournamentService;

    /**
     * LanController constructor.
     * @param TournamentServiceImpl $tournamentService
     */
    public function __construct(TournamentServiceImpl $tournamentService)
    {
        $this->tournamentService = $tournamentService;
    }

    public function createTournament(Request $request)
    {
        return response()->json($this->tournamentService->create($request), 201);
    }

    public function getAllTournament(Request $request)
    {
        return response()->json($this->tournamentService->getAll($request), 200);
    }

}