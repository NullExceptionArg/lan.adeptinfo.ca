<?php

namespace Tests\Unit\Service\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetRulesTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    protected $lan;

    public function setUp()
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testGetRules()
    {
        $result = $this->lanService->getRules($this->lan->id);

        $this->assertEquals($this->lan->rules, $result['text']);
    }

    public function testGetRulesLanIdExist()
    {
        $badLanId = -1;
        try {
            $this->lanService->getRules($badLanId);
            $this->fail('Expected: {"lan_id":["Lan with id ' . $badLanId . ' doesn\'t exist"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testGetRulesLanIdInteger()
    {
        $badLanId = '☭';
        try {
            $this->lanService->getRules($badLanId);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}
