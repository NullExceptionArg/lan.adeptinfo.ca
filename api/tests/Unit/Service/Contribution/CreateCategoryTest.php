<?php

namespace Tests\Unit\Service\Contribution;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributorService;

    protected $lan;

    protected $paramsContent = [
        'name' => "Programmer",
    ];

    public function setUp()
    {
        parent::setUp();
        $this->contributorService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testCreateCategory()
    {
        $request = new Request($this->paramsContent);
        $result = $this->contributorService->createCategory($request, $this->lan->id);

        $this->assertEquals($this->paramsContent['name'], $result['name']);
    }

    public function testCreateCategoryLanIdExist()
    {
        $badLanId = -1;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createCategory($request, $badLanId);
            $this->fail('Expected: {"lan_id":["Lan with id ' . $badLanId . ' doesn\'t exist"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateCategoryLanIdInteger()
    {
        $badLanId = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createCategory($request, $badLanId);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateCategoryNameRequired()
    {
        $this->paramsContent['name'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createCategory($request, $this->lan->id);
            $this->fail('Expected: {"name":["The name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateCategoryNameString()
    {
        $this->paramsContent['name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createCategory($request, $this->lan->id);
            $this->fail('Expected: {"name":["The name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name must be a string."]}', $e->getMessage());
        }
    }
}
