<?php

namespace Tests\Unit\Repository\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class FindByIdTest extends TestCase
{
    use DatabaseMigrations;

    protected $userRepository;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->app->make('App\Repositories\Implementation\UserRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
    }

    public function testFindUserById(): void
    {
        $result = $this->userRepository->findById($this->user->id);

        $this->assertEquals($this->user->id, $result->id);
        $this->assertEquals($this->user->first_name, $result->first_name);
        $this->assertEquals($this->user->last_name, $result->last_name);
        $this->assertEquals($this->user->email, $result->email);
    }
}
