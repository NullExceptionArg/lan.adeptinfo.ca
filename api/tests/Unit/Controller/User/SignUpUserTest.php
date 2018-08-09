<?php

namespace Tests\Unit\Controller\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class SignUpUserTest extends TestCase
{

    use DatabaseMigrations;

    protected $requestContent = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@doe.com',
        'password' => 'Passw0rd!'
    ];

    public function testSignUp(): void
    {
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                'first_name' => $this->requestContent['first_name'],
                'last_name' => $this->requestContent['last_name'],
                'email' => $this->requestContent['email']
            ])
            ->assertResponseStatus(201);
    }

    public function testSignUpEmailRequired(): void
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

    public function testSignUpEmailFormattedEmail(): void
    {
        $this->requestContent['email'] = 'john.doe.com';
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'email' => [
                        0 => 'The email must be a valid email address.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testSignUpPasswordRequired(): void
    {
        $this->requestContent['password'] = '';
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'password' => [
                        0 => 'The password field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testSignUpPasswordMinLength(): void
    {
        $this->requestContent['password'] = str_repeat('☭', 2);
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'password' => [
                        0 => 'The password must be at least 6 characters.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testSignUpPasswordMaxLength(): void
    {
        $this->requestContent['password'] = str_repeat('☭', 22);
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'password' => [
                        0 => 'The password may not be greater than 20 characters.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testSignUpFirstNameRequired(): void
    {
        $this->requestContent['first_name'] = '';
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'first_name' => [
                        0 => 'The first name field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testSignUpFirstNameMaxLength(): void
    {
        $this->requestContent['first_name'] = str_repeat('☭', 256);
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'first_name' => [
                        0 => 'The first name may not be greater than 255 characters.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testSignUpLastNameRequired(): void
    {
        $this->requestContent['last_name'] = '';
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'last_name' => [
                        0 => 'The last name field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testSignUpLastNameMaxLength(): void
    {
        $this->requestContent['last_name'] = str_repeat('☭', 256);
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'last_name' => [
                        0 => 'The last name may not be greater than 255 characters.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testSignUpUniqueEmailSocialLoginReturningFacebook(): void
    {
        factory('App\Model\User')->create([
            'facebook_id' => '12345678',
            'first_name' => $this->requestContent['first_name'],
            'last_name' => $this->requestContent['last_name'],
            'email' => $this->requestContent['email'],
            'password' => null
        ]);
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                'first_name' => $this->requestContent['first_name'],
                'last_name' => $this->requestContent['last_name'],
                'email' => $this->requestContent['email']
            ])
            ->assertResponseStatus(201);
    }

    public function testSignUpUniqueEmailSocialLoginAlreadyAwaitingConfirmation(): void
    {
        $this->call('POST', '/api/user', $this->requestContent);
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'email' => [
                        0 => 'The email has already been taken.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

}
