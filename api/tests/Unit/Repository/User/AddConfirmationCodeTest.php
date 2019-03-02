<?php

namespace Tests\Unit\Repository\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddConfirmationCodeTest extends TestCase
{
    use DatabaseMigrations;

    protected $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->app->make('App\Repositories\Implementation\UserRepositoryImpl');
    }

    public function testAddConfirmationCode(): void
    {
        $user = factory('App\Model\User')->create();
        $confirmationCode = '☭';

        $this->notSeeInDatabase('user', [
            'id' => $user->id,
            'confirmation_code' => $confirmationCode
        ]);

        $this->userRepository->addConfirmationCode($user->email, $confirmationCode);

        $this->seeInDatabase('user', [
            'id' => $user->id,
            'confirmation_code' => $confirmationCode
        ]);
    }
}
