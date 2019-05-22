<?php

namespace Tests\Unit\Repository\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteUserByIdTest extends TestCase
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

    public function testDeleteUserById(): void
    {
        $this->seeInDatabase('user', [
            'id'         => $this->user->id,
            'first_name' => $this->user->first_name,
            'last_name'  => $this->user->last_name,
            'email'      => $this->user->email,
        ]);

        $this->userRepository->deleteUserById($this->user->id);

        $this->notSeeInDatabase('user', [
            'id'         => $this->user->id,
            'first_name' => $this->user->first_name,
            'last_name'  => $this->user->last_name,
            'email'      => $this->user->email,
        ]);
    }
}
