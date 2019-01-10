<?php

namespace Tests\Unit\Repository;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteTournamentOrganizerTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentRepository = $this->app->make('App\Repositories\Implementation\TournamentRepositoryImpl');
    }

    public function testdeleteTournamentOrganizer(): void
    {
        $result = $this->tournamentRepository->deleteTournamentOrganizer();
    }
}
