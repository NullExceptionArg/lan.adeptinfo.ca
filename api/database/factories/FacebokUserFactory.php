<?php

use GuzzleHttp\Client;

$factory->state(App\Model\User::class, 'facebook', function (Faker\Generator $faker) {

    $client = new Client([
        'base_uri' => env('FB_GRAPH_HOST'),
        'timeout' => 10,
    ]);

    $accessToken = \GuzzleHttp\json_decode($client->request('GET', 'oauth/access_token', ['query' => [
        'client_id' => env('FB_ID'),
        'client_secret' => env('FB_SECRET'),
        'grant_type' => 'client_credentials'
    ]])->getBody())->access_token;

    $user = \GuzzleHttp\json_decode($client->request('POST', '/v3.1/' . env('FB_ID') . '/accounts/test-users', [
        'headers' => ['Authorization' => 'Bearer ' . $accessToken],
        'json' => [
            'installed' => true,
            'permissions' => 'email'
        ],
    ])->getBody());

    $userInfo = \GuzzleHttp\json_decode($client->request('GET', '/v3.1/' . $user->id, [
        'headers' => ['Authorization' => 'Bearer ' . $accessToken]
    ])->getBody());

    $explodedName = explode(' ', $userInfo->name);
    return [
        'first_name' => $explodedName[0],
        'last_name' => $explodedName[count($explodedName) - 1],
        'email' => $user->email,
        'facebook_id' => $user->id
    ];
});