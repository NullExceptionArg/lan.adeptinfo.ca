<?php

namespace Tests\Unit\Service\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class SignUpUserTest extends TestCase
{
    use DatabaseMigrations;

    protected $userService;

    protected $paramsContent = [
        'first_name' => 'John',
        'last_name'  => 'Doe',
        'email'      => 'john@doe.com',
        'password'   => 'Passw0rd!',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\Implementation\UserServiceImpl');
    }

    public function testSignUp(): void
    {
        $result = $this->userService->signUpUser(
            $this->paramsContent['first_name'],
            $this->paramsContent['last_name'],
            $this->paramsContent['email'],
            $this->paramsContent['password']
        );

        $this->assertEquals($this->paramsContent['first_name'], $result->first_name);
        $this->assertEquals($this->paramsContent['last_name'], $result->last_name);
        $this->assertEquals($this->paramsContent['email'], $result->email);
    }
}
