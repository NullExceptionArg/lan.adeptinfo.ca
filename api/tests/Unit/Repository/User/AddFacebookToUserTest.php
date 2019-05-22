<?php

namespace Tests\Unit\Repository\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddFacebookToUserTest extends TestCase
{
    use DatabaseMigrations;

    protected $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->app->make('App\Repositories\Implementation\UserRepositoryImpl');
    }

    public function testAddFacebookToUser(): void
    {
        $user = factory('App\Model\User')->create();
        $facebookId = '☭';

        $this->notSeeInDatabase('user', [
            'id'          => $user->id,
            'facebook_id' => $facebookId,
        ]);

        $this->userRepository->addFacebookToUser($user->email, $facebookId);

        $this->seeInDatabase('user', [
            'id'          => $user->id,
            'facebook_id' => $facebookId,
        ]);
    }
}
