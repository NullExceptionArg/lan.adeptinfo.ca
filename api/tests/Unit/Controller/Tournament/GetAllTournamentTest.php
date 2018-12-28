<?php

namespace Tests\Unit\Controller\Tournament;

use App\Model\TagTeam;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetAllTournamentTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $tag;
    protected $lan;

    protected $requestContent = [
        'lan_id' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id
        ]);
    }

    public function testGetAllTournamentHidden(): void
    {
        $this->lan = factory('App\Model\Lan')->create();
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->user->id,
            'tournament_id' => $tournament->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/tournament/all', $this->requestContent)
            ->seeJsonEquals([[
                'id' => $tournament->id,
                'name' => $tournament->name,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($tournament->tournament_start)),
                'tournament_end' => date('Y-m-d H:i:s', strtotime($tournament->tournament_end)),
                'state' => 'hidden',
                'teams_reached' => 0,
                'teams_to_reach' => $tournament->teams_to_reach,
            ]])
            ->assertResponseStatus(200);
    }

    public function testGetAllTournamentFinished(): void
    {
        $this->lan = factory('App\Model\Lan')->create();
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1),
            'state' => 'finished'
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->user->id,
            'tournament_id' => $tournament->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/tournament/all', $this->requestContent)
            ->seeJsonEquals([[
                'id' => $tournament->id,
                'name' => $tournament->name,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($tournament->tournament_start)),
                'tournament_end' => date('Y-m-d H:i:s', strtotime($tournament->tournament_end)),
                'state' => 'finished',
                'teams_reached' => 0,
                'teams_to_reach' => $tournament->teams_to_reach,
            ]])
            ->assertResponseStatus(200);
    }

    public function testGetAllTournamentFourthcoming(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'lan_start' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
            'lan_end' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1),
            'state' => 'visible'
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->user->id,
            'tournament_id' => $tournament->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/tournament/all', $this->requestContent)
            ->seeJsonEquals([[
                'id' => $tournament->id,
                'name' => $tournament->name,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($tournament->tournament_start)),
                'tournament_end' => date('Y-m-d H:i:s', strtotime($tournament->tournament_end)),
                'state' => 'fourthcoming',
                'teams_reached' => 0,
                'teams_to_reach' => $tournament->teams_to_reach,
            ]])
            ->assertResponseStatus(200);
    }

    public function testGetAllTournamentLate(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'lan_start' => Carbon::now()->addDays(-1)->format('Y-m-d H:i:s'),
            'lan_end' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1),
            'state' => 'visible'
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->user->id,
            'tournament_id' => $tournament->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/tournament/all', $this->requestContent)
            ->seeJsonEquals([[
                'id' => $tournament->id,
                'name' => $tournament->name,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($tournament->tournament_start)),
                'tournament_end' => date('Y-m-d H:i:s', strtotime($tournament->tournament_end)),
                'state' => 'late',
                'teams_reached' => 0,
                'teams_to_reach' => $tournament->teams_to_reach,
            ]])
            ->assertResponseStatus(200);
    }

    public function testGetAllTournamentOutguessed(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'lan_start' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
            'lan_end' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1),
            'state' => 'started'
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->user->id,
            'tournament_id' => $tournament->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/tournament/all', $this->requestContent)
            ->seeJsonEquals([[
                'id' => $tournament->id,
                'name' => $tournament->name,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($tournament->tournament_start)),
                'tournament_end' => date('Y-m-d H:i:s', strtotime($tournament->tournament_end)),
                'state' => 'outguessed',
                'teams_reached' => 0,
                'teams_to_reach' => $tournament->teams_to_reach,
            ]])
            ->assertResponseStatus(200);
    }

    public function testGetAllTournamentBehindhand(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'lan_start' => Carbon::now()->addDays(-1)->format('Y-m-d H:i:s'),
            'lan_end' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = new Carbon($this->lan->lan_start);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $startTime->addHour(2),
            'state' => 'started'
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->user->id,
            'tournament_id' => $tournament->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/tournament/all', $this->requestContent)
            ->seeJsonEquals([[
                'id' => $tournament->id,
                'name' => $tournament->name,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($tournament->tournament_start)),
                'tournament_end' => date('Y-m-d H:i:s', strtotime($tournament->tournament_end)),
                'state' => 'behindhand',
                'teams_reached' => 0,
                'teams_to_reach' => $tournament->teams_to_reach,
            ]])
            ->assertResponseStatus(200);
    }

    public function testGetAllTournamentRunning(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'lan_start' => Carbon::now()->addDays(-1)->format('Y-m-d H:i:s'),
            'lan_end' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1),
            'state' => 'started'
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->user->id,
            'tournament_id' => $tournament->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/tournament/all', $this->requestContent)
            ->seeJsonEquals([[
                'id' => $tournament->id,
                'name' => $tournament->name,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($tournament->tournament_start)),
                'tournament_end' => date('Y-m-d H:i:s', strtotime($tournament->tournament_end)),
                'state' => 'running',
                'teams_reached' => 0,
                'teams_to_reach' => $tournament->teams_to_reach,
            ]])
            ->assertResponseStatus(200);
    }

    public function testGetAllTournamentTeamsReachedTeamFull(): void
    {
        $this->lan = factory('App\Model\Lan')->create();
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->user->id,
            'tournament_id' => $tournament->id
        ]);

        $users = factory('App\Model\User', $tournament->players_to_reach)->create();
        $team = factory('App\Model\Team')->create([
            'tournament_id' => $tournament->id
        ]);
        foreach ($users as $user) {
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id
            ]);
            $tagTeam = new TagTeam();
            $tagTeam->tag_id = $tag->id;
            $tagTeam->team_id = $team->id;
            $tagTeam->save();
        }

        $this->actingAs($this->user)
            ->json('GET', '/api/tournament/all', $this->requestContent)
            ->seeJsonEquals([[
                'id' => $tournament->id,
                'name' => $tournament->name,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($tournament->tournament_start)),
                'tournament_end' => date('Y-m-d H:i:s', strtotime($tournament->tournament_end)),
                'state' => 'hidden',
                'teams_reached' => 1,
                'teams_to_reach' => $tournament->teams_to_reach,
            ]])
            ->assertResponseStatus(200);
    }

    public function testGetAllTournamentTeamsReachedTeamEmpty(): void
    {
        $this->lan = factory('App\Model\Lan')->create();
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->user->id,
            'tournament_id' => $tournament->id
        ]);

        $users = factory('App\Model\User', $tournament->players_to_reach - 1)->create();
        $team = factory('App\Model\Team')->create([
            'tournament_id' => $tournament->id
        ]);
        foreach ($users as $user) {
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id
            ]);
            $tagTeam = new TagTeam();
            $tagTeam->tag_id = $tag->id;
            $tagTeam->team_id = $team->id;
            $tagTeam->save();
        }

        $this->actingAs($this->user)
            ->json('GET', '/api/tournament/all', $this->requestContent)
            ->seeJsonEquals([[
                'id' => $tournament->id,
                'name' => $tournament->name,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($tournament->tournament_start)),
                'tournament_end' => date('Y-m-d H:i:s', strtotime($tournament->tournament_end)),
                'state' => 'hidden',
                'teams_reached' => 0,
                'teams_to_reach' => $tournament->teams_to_reach,
            ]])
            ->assertResponseStatus(200);
    }

    public function testGetAllTournamentCurrentLan(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->user->id,
            'tournament_id' => $tournament->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/tournament/all', $this->requestContent)
            ->seeJsonEquals([[
                'id' => $tournament->id,
                'name' => $tournament->name,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($tournament->tournament_start)),
                'tournament_end' => date('Y-m-d H:i:s', strtotime($tournament->tournament_end)),
                'state' => 'hidden',
                'teams_reached' => 0,
                'teams_to_reach' => $tournament->teams_to_reach,
            ]])
            ->assertResponseStatus(200);
    }

    public function testGetAllTournamentLanInteger(): void
    {
        $this->requestContent['lan_id'] = '☭';
        $this->actingAs($this->user)
            ->json('GET', '/api/tournament/all', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The lan id must be an integer.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testGetAllTournamentLanExist(): void
    {
        $this->requestContent['lan_id'] = -1;
        $this->actingAs($this->user)
            ->json('GET', '/api/tournament/all', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The selected lan id is invalid.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
