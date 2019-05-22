<?php

namespace Tests\Unit\Repository\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class FindByConfirmationCodeTest extends TestCase
{
    use DatabaseMigrations;

    protected $userRepository;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->app->make('App\Repositories\Implementation\UserRepositoryImpl');

        $this->user = factory('App\Model\User')->create([
            'confirmation_code' => '☭',
        ]);
    }

    public function testFindUserById(): void
    {
        $result = $this->userRepository->findByConfirmationCode($this->user->confirmation_code);

        $this->assertEquals($this->user->id, $result->id);
        $this->assertEquals($this->user->first_name, $result->first_name);
        $this->assertEquals($this->user->last_name, $result->last_name);
        $this->assertEquals($this->user->email, $result->email);
    }
}
