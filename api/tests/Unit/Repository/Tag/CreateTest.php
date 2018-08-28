<?php

namespace Tests\Unit\Repository\Tag;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseMigrations;

    protected $tagRepository;

    protected $user;

    protected $paramsContent = [
        'name' => 'PRO',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->tagRepository = $this->app->make('App\Repositories\Implementation\TagRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
    }

    public function testCreate(): void
    {
        $this->tagRepository->create(
            $this->user,
            $this->paramsContent['name']
        );
        $this->seeInDatabase('tag', [
            'name' => $this->paramsContent['name']
        ]);
    }
}
