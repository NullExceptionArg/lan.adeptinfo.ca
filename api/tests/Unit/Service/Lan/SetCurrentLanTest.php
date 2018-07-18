<?php

namespace Tests\Unit\Service\Lan;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class SetCurrentLanTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    protected $user;
    protected $lan;

    public function setUp()
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testSetCurrentLanNoCurrentLan()
    {
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        $result = $this->lanService->setCurrentLan($request);

        $this->assertEquals($this->lan->id, $result);
    }

    public function testSetCurrentLanHasCurrentLan()
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $request = new Request([
            'lan_id' => $lan->id
        ]);
        $result = $this->lanService->setCurrentLan($request);

        $this->assertEquals($lan->id, $result);
    }

    public function testSetCurrentLanIdExist()
    {
        $request = new Request([
            'lan_id' => -1
        ]);
        try {
            $this->lanService->setCurrentLan($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testSetCurrentLanIdInteger()
    {
        $request = new Request([
            'lan_id' => '☭'
        ]);
        try {
            $this->lanService->setCurrentLan($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}
