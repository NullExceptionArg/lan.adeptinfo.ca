<?php

namespace App\Repositories\Implementation;

use App\Model\Team;
use App\Model\Tournament;
use App\Repositories\TeamRepository;

class TeamRepositoryImpl implements TeamRepository
{
    // TODO Tests
    public function create(
        Tournament $tournament,
        string $name,
        string $tag
    ): Team
    {
        $team = new Team();
        $team->tournament_id = $tournament->id;
        $team->name = $name;
        $team->tag = $tag;
        $team->save();

        return $team;
    }
}