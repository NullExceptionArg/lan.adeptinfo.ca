<?php

namespace Tests\Unit\Service\Lan;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetLanTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testGetLanSimple(): void
    {
        $request = new Request();
        $result = $this->lanService->getLan($request, $this->lan->id);

        $this->assertEquals($this->lan->id, $result['id']);
        $this->assertEquals($this->lan->name, $result['name']);
        $this->assertEquals($this->lan->lan_start, $result['lan_start']);
        $this->assertEquals($this->lan->lan_end, $result['lan_end']);
        $this->assertEquals($this->lan->seat_reservation_start, $result['seat_reservation_start']);
        $this->assertEquals($this->lan->tournament_reservation_start, $result['tournament_reservation_start']);
        $this->assertEquals($this->lan->latitude, $result['latitude']);
        $this->assertEquals($this->lan->longitude, $result['longitude']);
        $this->assertEquals($this->lan->secret_key, $result['secret_key']);
        $this->assertEquals($this->lan->event_key_id, $result['event_key_id']);
        $this->assertEquals($this->lan->public_key, $result['public_key']);
        $this->assertEquals($this->lan->price, $result['price']);
        $this->assertEquals($this->lan->rules, $result['rules']);
        $this->assertEquals($this->lan->description, $result['description']);
    }

    public function testGetLanParameters(): void
    {
        $request = new Request(['fields' => "lan_start,lan_start,lan_end,seat_reservation_start"]);
        $result = $this->lanService->getLan($request, $this->lan->id);

        $this->assertEquals($this->lan->id, $result['id']);
        $this->assertEquals($this->lan->lan_start, $result['lan_start']);
        $this->assertEquals($this->lan->lan_end, $result['lan_end']);
        $this->assertEquals($this->lan->seat_reservation_start, $result['seat_reservation_start']);
    }

    public function testGetRulesLanIdExist(): void
    {
        $badLanId = -1;
        $request = new Request();
        try {
            $this->lanService->getLan($request, $badLanId);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testGetRulesLanIdInteger(): void
    {
        $badLanId = '☭';
        $request = new Request();
        try {
            $this->lanService->getLan($request, $badLanId);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}
