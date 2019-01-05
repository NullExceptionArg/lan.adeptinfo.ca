<?php

namespace Tests\Unit\Controller\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class LogOut extends TestCase
{

    use DatabaseMigrations;

    protected $requestContent = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@doe.com',
        'password' => 'Passw0rd!'
    ];

    public function testLogOut(): void
    {
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                'first_name' => $this->requestContent['first_name'],
                'last_name' => $this->requestContent['last_name'],
                'email' => $this->requestContent['email']
            ])
            ->assertResponseStatus(201);
    }

    public function testLogOutEmailRequired(): void
    {
        $this->requestContent['email'] = '';
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'email' => [
                        0 => 'The email field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
