<?php

namespace App\Repositories;

use App\Model\Team;
use App\Model\Tournament;

interface TeamRepository
{
    public function create(
        Tournament $tournament,
        string $name,
        string $tag
    ): Team;
}