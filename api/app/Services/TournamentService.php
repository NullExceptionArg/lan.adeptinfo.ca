<?php

namespace App\Services;

use App\Model\Tournament;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface TournamentService
{
    public function create(Request $input): Tournament;

    public function edit(Request $input, string $tournamentId): Tournament;

    public function getAll(Request $input): AnonymousResourceCollection;

    public function delete(string $tournamentId): Tournament;

    public function quit(string $tournamentId): Tournament;

    public function addOrganizer(Request $request, string $tournamentId): Tournament;
}