<?php

namespace Tests\Unit\Service\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use DatabaseMigrations;

    protected $userService;

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\Implementation\UserServiceImpl');
    }

    public function testDeleteUserSimple(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);

        $result = $this->userService->deleteUser($user->id);
        $this->assertEquals(null, $result);
    }
}
