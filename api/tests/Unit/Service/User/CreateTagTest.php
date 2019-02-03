<?php

namespace Tests\Unit\Service\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateTagTest extends TestCase
{
    use DatabaseMigrations;

    protected $userService;

    protected $user;

    protected $requestContent = [
        'name' => 'PRO'
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\Implementation\UserServiceImpl');

        $this->user = factory('App\Model\User')->create();
    }

    public function testCreate(): void
    {
        $result = $this->userService->createTag($this->user->id, $this->requestContent['name']);

        $this->assertEquals(1, $result->id);
        $this->assertEquals($this->requestContent['name'], $result->name);
    }
}
