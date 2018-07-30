<?php

namespace App\Services;

use App\Model\Tournament;
use Illuminate\Http\Request;

interface TournamentService
{
    public function create(Request $input): Tournament;
}