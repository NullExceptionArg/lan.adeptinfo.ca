<?php

namespace Tests\Unit\Controller\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetLanRulesTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetLanRules()
    {
        $lan = factory('App\Model\Lan')->create();

        $this->json('GET', '/api/lan/' . $lan->id . '/rules')
            ->seeJsonEquals([
                'text' => $lan->rules,
            ])
            ->assertResponseStatus(200);
    }

    public function testGetLanRulesLanIdExist()
    {
        $badLanId = -1;
        $this->json('GET', '/api/lan/' . $badLanId . '/rules')
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

    public function testGetLanRulesLanIdInteger()
    {
        $badLanId = '☭';
        $this->json('GET', '/api/lan/' . $badLanId . '/rules')
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
}
