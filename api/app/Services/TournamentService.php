<?php

namespace App\Services;

use App\Http\Resources\Tournament\TournamentDetailsResource;
use App\Http\Resources\Tournament\TournamentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface TournamentService
{
    public function create(Request $input): TournamentDetailsResource;

    public function edit(Request $input, string $tournamentId): TournamentDetailsResource;

    public function getAll(Request $input): AnonymousResourceCollection;

    public function getAllOrganizer(Request $input): AnonymousResourceCollection;

    public function get(string $tournamentId): TournamentDetailsResource;

    public function delete(string $tournamentId): TournamentResource;

    public function quit(string $tournamentId): TournamentResource;

    public function addOrganizer(Request $request, string $tournamentId): TournamentResource;
}