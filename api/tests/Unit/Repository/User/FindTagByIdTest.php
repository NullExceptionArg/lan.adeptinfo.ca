<?php

namespace Tests\Unit\Repository\User;

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
        $this->teamRepository = $this->app->make('App\Repositories\Implementation\UserRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
    }

    public function testFindTagById(): void
    {
        $result = $this->teamRepository->findTagById(
            $this->tag->id
        );
        $this->assertEquals($result->id, $this->tag->id);
        $this->assertEquals($result->name, $this->tag->name);
    }
}
