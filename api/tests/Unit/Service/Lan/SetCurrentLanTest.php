<?php

namespace Tests\Unit\Service\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class SetCurrentLanTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');

        $this->user = factory('App\Model\User')->create();
    }

    public function testSetCurrentLanHasCurrentLan()
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $result = $this->lanService->setCurrentLan($lan->id);

        $this->assertEquals($lan->id, $result);
    }

    public function testSetCurrentLanNoCurrentLan()
    {
        $lan = factory('App\Model\Lan')->create();
        $result = $this->lanService->setCurrentLan($lan->id);

        $this->assertEquals($lan->id, $result);
    }

    public function testSetCurrentLanIdExist()
    {
        $badLanId = -1;
        try {
            $this->lanService->setCurrentLan($badLanId);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testSetCurrentLanIdInteger()
    {
        $badLanId = '☭';
        try {
            $this->lanService->setCurrentLan($badLanId);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}
