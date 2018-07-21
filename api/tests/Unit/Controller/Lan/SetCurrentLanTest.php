<?php

namespace Tests\Unit\Controller\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class SetCurrentLanTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
    }

    public function testSetCurrentLanNoCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $response = $this->actingAs($this->user)
            ->call('POST', '/api/lan/current', [
                'lan_id' => $lan->id
            ]);

        $this->assertEquals($lan->id, $response->content());
        $this->assertEquals(200, $response->status());
    }

    public function testSetCurrentLanHasCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $response = $this->actingAs($this->user)
            ->call('POST', '/api/lan/current', [
                'lan_id' => $lan->id
            ]);

        $this->assertEquals($lan->id, $response->content());
        $this->assertEquals(200, $response->status());
    }

    public function testSetCurrentLanIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/current', [
                'lan_id' => -1
            ])
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

    public function testSetCurrentLanIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/current', [
                'lan_id' => '☭'
            ])
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
