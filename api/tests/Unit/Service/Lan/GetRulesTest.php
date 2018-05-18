<?php

namespace Tests\Unit\Service\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetRulesTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    public function setUp()
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');
    }

    public function testGetRules()
    {
        $lan = factory('App\Model\Lan')->create();

        $result = $this->lanService->getRules($lan->id);

        $this->assertEquals($lan->rules, $result['text']);
    }

    public function testGetRulesLanIdExist()
    {
        $badLanId = -1;
        try {
            $this->lanService->getRules($badLanId);
            $this->fail('Expected: {"lan_id":["Lan with id ' . $badLanId . ' doesn\'t exist"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["Lan with id ' . $badLanId . ' doesn\'t exist"]}', $e->getMessage());
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
