<?php

namespace Tests\Unit\Controller\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class SignInGoogleTest extends TestCase
{

    use DatabaseMigrations;

    public function testSignInGoogleValidGoogleToken(): void
    {
        $this->json('POST', 'http://' . env('API_DOMAIN') . '/user/google', ['access_token' => '☭'])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'access_token' => [
                        0 => 'Invalid Google token.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
