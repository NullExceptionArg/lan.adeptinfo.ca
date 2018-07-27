<?php

namespace App\Services;

use App\Model\Request as TeamRequest;
use App\Model\Team;
use Illuminate\Http\Request;

interface TeamService
{
    public function create(Request $input): Team;

    public function createRequest(Request $input): TeamRequest;
}