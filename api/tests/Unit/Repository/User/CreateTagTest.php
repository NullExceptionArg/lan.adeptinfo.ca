<?php

namespace Tests\Unit\Repository\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateTagTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamRepository;

    protected $user;

    protected $paramsContent = [
        'name' => 'PRO',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = $this->app->make('App\Repositories\Implementation\UserRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
    }

    public function testCreateTag(): void
    {
        $this->notSeeInDatabase('tag', [
            'name' => $this->paramsContent['name']
        ]);

        $this->teamRepository->createTag(
            $this->user->id,
            $this->paramsContent['name']
        );

        $this->seeInDatabase('tag', [
            'name' => $this->paramsContent['name']
        ]);
    }
}
