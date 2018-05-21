<?php

namespace Tests\Unit\Service\Lan;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class UpdateRulesTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    protected $lan;

    protected $paramsContent = [
        'text' => "☭"
    ];

    public function setUp()
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testUpdateLanRules()
    {
        $request = new Request($this->paramsContent);
        $result = $this->lanService->updateRules($request, $this->lan->id);

        $this->assertEquals($this->paramsContent['text'], $result['text']);
    }

    public function testUpdateRulesLanIdExist()
    {
        $badLanId = -1;
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->updateRules($request, $badLanId);
            $this->fail('Expected: {"lan_id":["Lan with id ' . $badLanId . ' doesn\'t exist"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testUpdateRulesLanIdInteger()
    {
        $badLanId = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->updateRules($request, $badLanId);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testUpdateRulesTextRequired()
    {
        $this->paramsContent['text'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->updateRules($request, $this->lan->id);
            $this->fail('Expected: {"text":["The text field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"text":["The text field is required."]}', $e->getMessage());
        }
    }

    public function testUpdateRulesTextString()
    {
        $this->paramsContent['text'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->updateRules($request, $this->lan->id);
            $this->fail('Expected: {"text":["The text must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"text":["The text must be a string."]}', $e->getMessage());
        }
    }
}
