<?php

namespace Tests\Unit\Controller\User;

use App\Utils\FacebookUtils;
use Facebook\Exceptions\FacebookSDKException;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\FacebookTestCase;

class SignInFacebookTest extends FacebookTestCase
{
    use DatabaseMigrations;

    public function testSignInFacebookNewUser(): void
    {
        $this->artisan('passport:install');
        $accessToken = null;

        try {
            $accessToken = FacebookUtils::getFacebook()->post(
                '/'.env('FB_ID').'/accounts/test-users?permissions=email',
                [
                    'installed' => 'true',
                ],
                FacebookUtils::getAccessToken()->getValue()
            )->getDecodedBody()['access_token'];
        } catch (FacebookSDKException $e) {
        }

        $this->json('POST', 'http://'.env('API_DOMAIN').'/user/facebook', ['access_token' => $accessToken])
            ->seeJsonStructure([
                'token',
            ])
            ->assertResponseStatus(201);
    }

    public function testSignInFacebookReturningUser(): void
    {
        $this->artisan('passport:install');
        $response = null;

        try {
            $response = FacebookUtils::getFacebook()->post(
                '/'.env('FB_ID').'/accounts/test-users?permissions=email',
                [
                    'installed' => 'true',
                ],
                FacebookUtils::getAccessToken()->getValue()
            )->getDecodedBody();
        } catch (FacebookSDKException $e) {
        }

        $accessToken = $response['access_token'];
        $email = $response['email'];

        factory('App\Model\User')->create([
            'email' => $email,
        ]);

        $this->json('POST', 'http://'.env('API_DOMAIN').'/user/facebook', ['access_token' => $accessToken])
            ->seeJsonStructure([
                'token',
            ])
            ->assertResponseStatus(200);
    }

    public function testSignInFacebookValidFacebookToken(): void
    {
        $this->json('POST', 'http://'.env('API_DOMAIN').'/user/facebook', ['access_token' => '☭'])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'access_token' => [
                        0 => 'Invalid Facebook token.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testSignInFacebookEmailPermission(): void
    {
        $accessToken = null;

        try {
            $accessToken = FacebookUtils::getFacebook()->post(
                '/'.env('FB_ID').'/accounts/test-users',
                [
                    'installed' => 'true',
                ],
                FacebookUtils::getAccessToken()->getValue()
            )->getDecodedBody()['access_token'];
        } catch (FacebookSDKException $e) {
        }

        $this->json('POST', 'http://'.env('API_DOMAIN').'/user/facebook', ['access_token' => $accessToken])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'access_token' => [
                        0 => 'The email permission must be provided.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
