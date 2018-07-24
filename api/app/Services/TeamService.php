<?php

namespace App\Services;

use App\Model\Team;
use Illuminate\Http\Request;

interface TeamService
{
    public function create(Request $input): Team;
}