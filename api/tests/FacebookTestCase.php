<?php

namespace Tests;

use GuzzleHttp\Client;

abstract class FacebookTestCase extends TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function tearDown()
    {
        $client = new Client([
            'base_uri' => env('FB_GRAPH_HOST'),
            'timeout' => 10,
        ]);

        $accessToken = \GuzzleHttp\json_decode($client->request('GET', 'oauth/access_token', ['query' => [
            'client_id' => env('FB_ID'),
            'client_secret' => env('FB_SECRET'),
            'grant_type' => 'client_credentials'
        ]])->getBody())->access_token;

        $users = \GuzzleHttp\json_decode($client->request('GET', '/v3.1/' . env('FB_ID') . '/accounts/test-users', [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken]
        ])->getBody())->data;

        foreach ($users as $user) {
            \GuzzleHttp\json_decode($client->request('DELETE', '/v3.1/' . $user->id, [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken]
            ])->getBody());
        }

        parent::tearDown();
    }
}
