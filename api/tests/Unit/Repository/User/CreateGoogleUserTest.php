<?php

namespace Tests\Unit\Repository\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateGoogleUserTest extends TestCase
{
    use DatabaseMigrations;

    protected $userRepository;

    protected $params = [
        'google_id' => '☭',
        'first_name' => 'karl',
        'last_name' => 'marx',
        'email' => 'karl.marx@unite.org',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->app->make('App\Repositories\Implementation\UserRepositoryImpl');
    }

    public function testCreateGoogleUser(): void
    {
        $this->notSeeInDatabase('user', [
            'google_id' => $this->params['google_id'],
            'first_name' => $this->params['first_name'],
            'last_name' => $this->params['last_name'],
            'email' => $this->params['email']
        ]);

        $this->userRepository->createGoogleUser(
            $this->params['google_id'],
            $this->params['first_name'],
            $this->params['last_name'],
            $this->params['email']
        );

        $this->seeInDatabase('user', [
            'google_id' => $this->params['google_id'],
            'first_name' => $this->params['first_name'],
            'last_name' => $this->params['last_name'],
            'email' => $this->params['email']
        ]);
    }
}
