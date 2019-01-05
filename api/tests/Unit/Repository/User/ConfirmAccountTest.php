<?php

namespace Tests\Unit\Repository\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class ConfirmAccountTest extends TestCase
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
            'is_confirmed' => false
        ]);
    }

    public function testFindUserById(): void
    {
        $this->seeInDatabase('user', [
            'id' => $this->user->id,
            'confirmation_code' => $this->user->confirmation_code,
            'is_confirmed' => $this->user->is_confirmed
        ]);

        $this->userRepository->confirmAccount($this->user->id);

        $this->seeInDatabase('user', [
            'id' => $this->user->id,
            'confirmation_code' => null,
            'is_confirmed' => true
        ]);
    }
}
