<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetRequestsTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament1;
    protected $tournament2;
    protected $team1;
    protected $team2;
    protected $team3;
    protected $team4;
    protected $request1;
    protected $request2;
    protected $request3;

    protected $requestContent = [
        'lan_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->teamService = $this->app->make('App\Services\Implementation\TeamServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id
        ]);

        $this->lan = factory('App\Model\Lan')->create();

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

        $this->team1 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament1->id
        ]);
        $this->team2 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament1->id
        ]);
        $this->team3 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament1->id
        ]);
        $this->team4 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament2->id
        ]);

        $this->request1 = factory('App\Model\Request')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team1->id
        ]);
        $this->request2 = factory('App\Model\Request')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team2->id
        ]);
        $this->request3 = factory('App\Model\Request')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team4->id
        ]);

        $this->requestContent['lan_id'] = $this->lan->id;

        $this->be($this->user);
    }

    public function testGetRequests(): void
    {
        $request = new Request($this->requestContent);
        $result = $this->teamService->getRequests($request);

        $this->assertEquals($this->request1->id, $result[0]->id);
        $this->assertEquals($this->tag->id, $result[0]->tag_id);
        $this->assertEquals($this->tag->name, $result[0]->tag_name);
        $this->assertEquals($this->team1->id, $result[0]->team_id);
        $this->assertEquals($this->team1->tag, $result[0]->team_tag);
        $this->assertEquals($this->team1->name, $result[0]->team_name);
        $this->assertEquals($this->tournament1->id, $result[0]->tournament_id);
        $this->assertEquals($this->tournament1->name, $result[0]->tournament_name);

        $this->assertEquals($this->request2->id, $result[1]->id);
        $this->assertEquals($this->tag->id, $result[1]->tag_id);
        $this->assertEquals($this->tag->name, $result[1]->tag_name);
        $this->assertEquals($this->team2->id, $result[1]->team_id);
        $this->assertEquals($this->team2->tag, $result[1]->team_tag);
        $this->assertEquals($this->team2->name, $result[1]->team_name);
        $this->assertEquals($this->tournament1->id, $result[1]->tournament_id);
        $this->assertEquals($this->tournament1->name, $result[1]->tournament_name);

        $this->assertEquals($this->request3->id, $result[2]->id);
        $this->assertEquals($this->tag->id, $result[2]->tag_id);
        $this->assertEquals($this->tag->name, $result[2]->tag_name);
        $this->assertEquals($this->team4->id, $result[2]->team_id);
        $this->assertEquals($this->team4->tag, $result[2]->team_tag);
        $this->assertEquals($this->team4->name, $result[2]->team_name);
        $this->assertEquals($this->tournament2->id, $result[2]->tournament_id);
        $this->assertEquals($this->tournament2->name, $result[2]->tournament_name);
    }

    public function testGetRequestsCurrentLan(): void
    {
        $this->lan->is_current = true;
        $this->lan->save();
        $this->requestContent['lan_id'] = null;

        $request = new Request($this->requestContent);
        $result = $this->teamService->getRequests($request);

        $this->assertEquals($this->request1->id, $result[0]->id);
        $this->assertEquals($this->tag->id, $result[0]->tag_id);
        $this->assertEquals($this->tag->name, $result[0]->tag_name);
        $this->assertEquals($this->team1->id, $result[0]->team_id);
        $this->assertEquals($this->team1->tag, $result[0]->team_tag);
        $this->assertEquals($this->team1->name, $result[0]->team_name);
        $this->assertEquals($this->tournament1->id, $result[0]->tournament_id);
        $this->assertEquals($this->tournament1->name, $result[0]->tournament_name);

        $this->assertEquals($this->request2->id, $result[1]->id);
        $this->assertEquals($this->tag->id, $result[1]->tag_id);
        $this->assertEquals($this->tag->name, $result[1]->tag_name);
        $this->assertEquals($this->team2->id, $result[1]->team_id);
        $this->assertEquals($this->team2->tag, $result[1]->team_tag);
        $this->assertEquals($this->team2->name, $result[1]->team_name);
        $this->assertEquals($this->tournament1->id, $result[1]->tournament_id);
        $this->assertEquals($this->tournament1->name, $result[1]->tournament_name);

        $this->assertEquals($this->request3->id, $result[2]->id);
        $this->assertEquals($this->tag->id, $result[2]->tag_id);
        $this->assertEquals($this->tag->name, $result[2]->tag_name);
        $this->assertEquals($this->team4->id, $result[2]->team_id);
        $this->assertEquals($this->team4->tag, $result[2]->team_tag);
        $this->assertEquals($this->team4->name, $result[2]->team_name);
        $this->assertEquals($this->tournament2->id, $result[2]->tournament_id);
        $this->assertEquals($this->tournament2->name, $result[2]->tournament_name);
    }

    public function testGetRequestsLanIdInteger(): void
    {
        $this->requestContent['lan_id'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->teamService->getRequests($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testGetRequestsLanIdExist(): void
    {
        $this->requestContent['lan_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->getRequests($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }
}
