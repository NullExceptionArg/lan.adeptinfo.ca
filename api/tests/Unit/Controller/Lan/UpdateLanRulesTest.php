<?php

namespace Tests\Unit\Controller\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateLanRulesTest extends TestCase
{

    use DatabaseMigrations;

    protected $requestContent = [
        'text' => "☭",
    ];

    public function testUpdateLanRules()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $lan->id . '/rules', $this->requestContent)
            ->seeJsonEquals([
                'text' => $this->requestContent['text'],
            ])
            ->assertResponseStatus(201);
    }

    public function testUpdateLanRulesLanIdExist()
    {
        $user = factory('App\Model\User')->create();
        $badLanId = -1;

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $badLanId . '/rules', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The selected lan id is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdateLanRulesLanIdInteger()
    {
        $user = factory('App\Model\User')->create();
        $badLanId = '☭';

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $badLanId . '/rules', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The lan id must be an integer.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdateLanRulesTextRequired()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $this->requestContent['text'] = null;

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $lan->id . '/rules', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'text' => [
                        0 => 'The text field is required.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdateLanRulesTextString()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $this->requestContent['text'] = 1;

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $lan->id . '/rules', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'text' => [
                        0 => 'The text must be a string.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
