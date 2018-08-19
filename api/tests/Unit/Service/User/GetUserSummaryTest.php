<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetUserSummaryTest extends TestCase
{
    use DatabaseMigrations;

    protected $userService;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament1;
    protected $tournament2;
    protected $tournament3;
    protected $team1;
    protected $team2;
    protected $team3;

    protected $requestContent = [
        'lan_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\Implementation\UserServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id
        ]);
        $this->lan = factory('App\Model\Lan')->create();

        $this->requestContent['lan_id'] = $this->lan->id;

        $this->be($this->user);
    }

    public function testGetUserSummary(): void
    {
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $this->tournament1 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $this->tournament2 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $this->tournament3 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);

        $this->team1 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament1->id
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team1->id,
            'is_leader' => true
        ]);
        $this->team2 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament2->id
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team2->id,
            'is_leader' => true
        ]);
        $this->team3 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament3->id
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team3->id,
            'is_leader' => false
        ]);

        for ($i = 0; $i < 3; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id
            ]);
            factory('App\Model\Request')->create([
                'tag_id' => $tag->id,
                'team_id' => $this->team1->id
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id
            ]);
            factory('App\Model\Request')->create([
                'tag_id' => $tag->id,
                'team_id' => $this->team2->id
            ]);
        }

        for ($i = 0; $i < 4; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id
            ]);
            factory('App\Model\Request')->create([
                'tag_id' => $tag->id,
                'team_id' => $this->team3->id
            ]);
        }

        $request = new Request($this->requestContent);
        $result = $this->userService->getUserSummary($request)->jsonSerialize();

        $this->assertEquals($this->user->first_name, $result['first_name']);
        $this->assertEquals($this->user->last_name, $result['last_name']);
        $this->assertEquals(5, $result['request_count']);
    }

    public function testGetUserSummaryCurrentLan(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $this->requestContent['lan_id'] = null;

        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $this->tournament1 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $this->tournament2 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $this->tournament3 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);

        $this->team1 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament1->id
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team1->id,
            'is_leader' => true
        ]);
        $this->team2 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament2->id
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team2->id,
            'is_leader' => true
        ]);
        $this->team3 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament3->id
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team3->id,
            'is_leader' => false
        ]);

        for ($i = 0; $i < 3; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id
            ]);
            factory('App\Model\Request')->create([
                'tag_id' => $tag->id,
                'team_id' => $this->team1->id
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id
            ]);
            factory('App\Model\Request')->create([
                'tag_id' => $tag->id,
                'team_id' => $this->team2->id
            ]);
        }

        for ($i = 0; $i < 4; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id
            ]);
            factory('App\Model\Request')->create([
                'tag_id' => $tag->id,
                'team_id' => $this->team3->id
            ]);
        }

        $request = new Request($this->requestContent);
        $result = $this->userService->getUserSummary($request)->jsonSerialize();

        $this->assertEquals($this->user->first_name, $result['first_name']);
        $this->assertEquals($this->user->last_name, $result['last_name']);
        $this->assertEquals(5, $result['request_count']);
    }

    public function testCreateLanIdInteger(): void
    {
        $this->requestContent['lan_id'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->userService->getUserSummary($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateLanIdExist(): void
    {
        $this->requestContent['lan_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->userService->getUserSummary($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }
}
