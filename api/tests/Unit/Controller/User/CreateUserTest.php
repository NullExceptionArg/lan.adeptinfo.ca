<?php

namespace Tests\Unit\Controller\User;

use App\Model\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateUserTest extends TestCase
{

    use DatabaseMigrations;

    protected $requestContent = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@doe.com',
        'password' => 'Passw0rd!'
    ];

    public function testSignUp()
    {
        $this->json('POST', '/api/user', $this->requestContent)
            ->seeJsonEquals([
                "first_name" => $this->requestContent['first_name'],
                "last_name" => $this->requestContent['last_name'],
                "email" => $this->requestContent['email']
            ])
            ->assertResponseStatus(201);
    }

    public function testSignUpEmailRequired()
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

    public function testSignUpEmailFormattedEmail()
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

    public function testSignUpEmailUnique()
    {
        $this->requestContent['email'] = 'john@doe.com';
        $user = new User();
        $user->first_name = $this->requestContent['first_name'];
        $user->last_name = $this->requestContent['last_name'];
        $user->email = $this->requestContent['email'];
        $user->password = Hash::make($this->requestContent['password']);
        $user->save();
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

    public function testSignUpPasswordRequired()
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

    public function testSignUpPasswordMinLength()
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

    public function testSignUpPasswordMaxLength()
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

    public function testSignUpFirstNameRequired()
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

    public function testSignUpFirstNameMaxLength()
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

    public function testSignUpLastNameRequired()
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

    public function testSignUpLastNameMaxLength()
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
}
