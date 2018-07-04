<?php

namespace Tests\Unit\Controller\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateLanRulesTest extends TestCase
{

    use DatabaseMigrations;

    protected $user;
    protected $lan;

    protected $requestContent = [
        'rules' => "☭",
    ];

    public function setUp()
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testUpdateLanRules()
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/rules', $this->requestContent)
            ->seeJsonEquals([
                'rules' => $this->requestContent['rules'],
            ])
            ->assertResponseStatus(201);
    }

    public function testUpdateLanRulesLanIdExist()
    {
        $badLanId = -1;
        $this->actingAs($this->user)
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
        $badLanId = '☭';
        $this->actingAs($this->user)
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
        $this->requestContent['rules'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/rules', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'rules' => [
                        0 => 'The rules field is required.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdateLanRulesTextString()
    {
        $this->requestContent['rules'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/rules', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'rules' => [
                        0 => 'The rules must be a string.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
