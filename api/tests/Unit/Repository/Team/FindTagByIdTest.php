<?php

namespace Tests\Unit\Repository\Team;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class FindTagByIdTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamRepository;

    protected $user;
    protected $tag;

    public function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = $this->app->make('App\Repositories\Implementation\TeamRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
    }

    public function testFindTagById(): void
    {
        $result = $this->teamRepository->findTagById($this->tag->id);
        $this->assertEquals($this->tag->id, $result->id);
    }
}
