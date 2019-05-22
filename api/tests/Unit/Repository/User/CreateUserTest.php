<?php

namespace Tests\Unit\Repository\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use DatabaseMigrations;

    protected $userRepository;

    protected $paramsContent = [
        'first_name'        => 'John',
        'last_name'         => 'Doe',
        'email'             => 'john@doe.com',
        'password'          => 'Passw0rd!',
        'confirmation_code' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->app->make('App\Repositories\Implementation\UserRepositoryImpl');

        $this->paramsContent['confirmation_code'] = $confirmationCode = str_random(30);
    }

    public function testCreateUser(): void
    {
        $this->userRepository->createUser(
            $this->paramsContent['first_name'],
            $this->paramsContent['last_name'],
            $this->paramsContent['email'],
            $this->paramsContent['password'],
            $this->paramsContent['confirmation_code']
        );
        $this->seeInDatabase('user', [
            'first_name' => $this->paramsContent['first_name'],
            'last_name'  => $this->paramsContent['last_name'],
            'email'      => $this->paramsContent['email'],
        ]);
    }
}
