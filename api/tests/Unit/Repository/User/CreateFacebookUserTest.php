<?php

namespace Tests\Unit\Repository\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateFacebookUserTest extends TestCase
{
    use DatabaseMigrations;

    protected $userRepository;

    protected $params = [
        'facebook_id' => '☭',
        'first_name' => 'karl',
        'last_name' => 'marx',
        'email' => 'karl.marx@unite.org',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->app->make('App\Repositories\Implementation\UserRepositoryImpl');
    }

    public function testCreateFacebookUser(): void
    {
        $this->notSeeInDatabase('user', [
            'facebook_id' => $this->params['facebook_id'],
            'first_name' => $this->params['first_name'],
            'last_name' => $this->params['last_name'],
            'email' => $this->params['email']
        ]);

        $this->userRepository->createFacebookUser(
            $this->params['facebook_id'],
            $this->params['first_name'],
            $this->params['last_name'],
            $this->params['email']
        );

        $this->seeInDatabase('user', [
            'facebook_id' => $this->params['facebook_id'],
            'first_name' => $this->params['first_name'],
            'last_name' => $this->params['last_name'],
            'email' => $this->params['email']
        ]);
    }
}
