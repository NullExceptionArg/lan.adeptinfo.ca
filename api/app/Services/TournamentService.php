<?php

namespace App\Services;

use App\Model\Tournament;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface TournamentService
{
    public function create(Request $input): Tournament;

    public function getAll(Request $input): AnonymousResourceCollection;
}