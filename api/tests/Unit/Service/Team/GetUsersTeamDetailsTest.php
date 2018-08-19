<?php

namespace Tests\Unit\Service\Team;

use App\Model\TagTeam;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetUsersTeamDetailsTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

    protected $requestContent = [
        'team_id' => null
    ];

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $tagTeam;

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
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        $this->team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id
        ]);
        $this->tagTeam = factory('App\Model\TagTeam')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team->id
        ]);

        $this->requestContent['team_id'] = $this->team->id;
        $this->be($this->user);
    }

    public function testGetUsersTeamDetailsAdminRequests(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id
        ]);
        $tagTeam = factory('App\Model\TagTeam')->create([
            'tag_id' => $tag->id,
            'team_id' => $this->team->id,
            'is_leader' => true
        ]);

        $user2 = factory('App\Model\User')->create();
        $tag2 = factory('App\Model\Tag')->create([
            'user_id' => $user2->id
        ]);
        $tagTeam2 = factory('App\Model\Request')->create([
            'tag_id' => $tag2->id,
            'team_id' => $this->team->id,
        ]);
        $this->be($user);

        $request = new Request($this->requestContent);
        $result = $this->teamService->getUsersTeamDetails($request)->jsonSerialize();

        $this->assertEquals(1, $result['id']);
        $this->assertEquals($this->team->name, $result['name']);
        $this->assertEquals($this->team->tag, $result['team_tag']);

        $this->assertEquals($tagTeam->id, $result['user_tags'][1]->jsonSerialize()['id']);
        $this->assertEquals($tag->id, $result['user_tags'][1]->jsonSerialize()['tag_id']);
        $this->assertEquals($tag->name, $result['user_tags'][1]->jsonSerialize()['tag_name']);
        $this->assertEquals($user->first_name, $result['user_tags'][1]->jsonSerialize()['first_name']);
        $this->assertEquals($user->last_name, $result['user_tags'][1]->jsonSerialize()['last_name']);
        $this->assertEquals(true, $result['user_tags'][1]->jsonSerialize()['is_leader']);

        $this->assertEquals($this->tagTeam->id, $result['user_tags'][0]->jsonSerialize()['id']);
        $this->assertEquals($this->tag->id, $result['user_tags'][0]->jsonSerialize()['tag_id']);
        $this->assertEquals($this->tag->name, $result['user_tags'][0]->jsonSerialize()['tag_name']);
        $this->assertEquals($this->user->first_name, $result['user_tags'][0]->jsonSerialize()['first_name']);
        $this->assertEquals($this->user->last_name, $result['user_tags'][0]->jsonSerialize()['last_name']);
        $this->assertEquals(false, $result['user_tags'][0]->jsonSerialize()['is_leader']);

        $this->assertEquals($tagTeam2->id, $result['requests'][0]->jsonSerialize()['id']);
        $this->assertEquals($tag2->id, $result['requests'][0]->jsonSerialize()['tag_id']);
        $this->assertEquals($tag2->name, $result['requests'][0]->jsonSerialize()['tag_name']);
        $this->assertEquals($user2->first_name, $result['requests'][0]->jsonSerialize()['first_name']);
        $this->assertEquals($user2->last_name, $result['requests'][0]->jsonSerialize()['last_name']);
    }

    public function testGetUsersTeamDetailsAdminNoRequests(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id
        ]);
        $tagTeam = factory('App\Model\TagTeam')->create([
            'tag_id' => $tag->id,
            'team_id' => $this->team->id,
            'is_leader' => true
        ]);

        $this->be($user);

        $request = new Request($this->requestContent);
        $result = $this->teamService->getUsersTeamDetails($request)->jsonSerialize();

        $this->assertEquals(1, $result['id']);
        $this->assertEquals($this->team->name, $result['name']);
        $this->assertEquals($this->team->tag, $result['team_tag']);

        $this->assertEquals($tagTeam->id, $result['user_tags'][1]->jsonSerialize()['id']);
        $this->assertEquals($tag->id, $result['user_tags'][1]->jsonSerialize()['tag_id']);
        $this->assertEquals($tag->name, $result['user_tags'][1]->jsonSerialize()['tag_name']);
        $this->assertEquals($user->first_name, $result['user_tags'][1]->jsonSerialize()['first_name']);
        $this->assertEquals($user->last_name, $result['user_tags'][1]->jsonSerialize()['last_name']);
        $this->assertEquals(true, $result['user_tags'][1]->jsonSerialize()['is_leader']);

        $this->assertEquals($this->tagTeam->id, $result['user_tags'][0]->jsonSerialize()['id']);
        $this->assertEquals($this->tag->id, $result['user_tags'][0]->jsonSerialize()['tag_id']);
        $this->assertEquals($this->tag->name, $result['user_tags'][0]->jsonSerialize()['tag_name']);
        $this->assertEquals($this->user->first_name, $result['user_tags'][0]->jsonSerialize()['first_name']);
        $this->assertEquals($this->user->last_name, $result['user_tags'][0]->jsonSerialize()['last_name']);
        $this->assertEquals(false, $result['user_tags'][0]->jsonSerialize()['is_leader']);
    }

    public function testGetUsersTeamDetailsNotAdmin(): void
    {
        $request = new Request($this->requestContent);
        $result = $this->teamService->getUsersTeamDetails($request)->jsonSerialize();

        $this->assertEquals(1, $result['id']);
        $this->assertEquals($this->team->name, $result['name']);
        $this->assertEquals($this->team->tag, $result['team_tag']);

        $this->assertEquals($this->tagTeam->id, $result['user_tags'][0]->jsonSerialize()['id']);
        $this->assertEquals($this->tag->id, $result['user_tags'][0]->jsonSerialize()['tag_id']);
        $this->assertEquals($this->tag->name, $result['user_tags'][0]->jsonSerialize()['tag_name']);
        $this->assertEquals($this->user->first_name, $result['user_tags'][0]->jsonSerialize()['first_name']);
        $this->assertEquals($this->user->last_name, $result['user_tags'][0]->jsonSerialize()['last_name']);
        $this->assertEquals(false, $result['user_tags'][0]->jsonSerialize()['is_leader']);
    }

    public function testGetUsersTeamDetailsTeamIdInteger(): void
    {
        $this->requestContent['team_id'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->teamService->getUsersTeamDetails($request);
            $this->fail('Expected: {"team_id":["The team id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The team id must be an integer."]}', $e->getMessage());
        }
    }

    public function testGetUsersTeamDetailsTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->getUsersTeamDetails($request);
            $this->fail('Expected: {"team_id":["The selected team id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The selected team id is invalid."]}', $e->getMessage());
        }
    }

    public function testGetUsersTeamDetailsTeamIdUserBelongsInTeam(): void
    {
        TagTeam::where('team_id', $this->team->id)
            ->where('tag_id', $this->tag->id)
            ->delete();
        $request = new Request($this->requestContent);
        try {
            $this->teamService->getUsersTeamDetails($request);
            $this->fail('Expected: {"user_tag_id":["A user can only be once in a tournament."]}');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }
}