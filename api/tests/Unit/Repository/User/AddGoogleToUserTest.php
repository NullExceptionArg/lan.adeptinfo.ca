<?php

namespace Tests\Unit\Repository\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddGoogleToUserTest extends TestCase
{
    use DatabaseMigrations;

    protected $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->app->make('App\Repositories\Implementation\UserRepositoryImpl');
    }

    public function testAddGoogleToUser(): void
    {
        $user = factory('App\Model\User')->create();
        $googleId = '☭';

        $this->notSeeInDatabase('user', [
            'id'        => $user->id,
            'google_id' => $googleId,
        ]);

        $this->userRepository->addGoogleToUser($user->email, $googleId);

        $this->seeInDatabase('user', [
            'id'        => $user->id,
            'google_id' => $googleId,
        ]);
    }
}
