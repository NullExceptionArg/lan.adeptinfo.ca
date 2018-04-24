<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class UserRepositoryTest extends TestCase
{
    protected $userRepository;

    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = $this->app->make('App\Repository\Implementation\UserRepositoryImpl');
    }
}
