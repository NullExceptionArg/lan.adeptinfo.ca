<?php

namespace Tests\Unit\Service\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetCategoriesTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionService;

    public function setUp()
    {
        parent::setUp();
        $this->contributionService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');
    }

    public function testGetCategories()
    {
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);

        $result = $this->contributionService->getCategories($lan->id);

        $this->assertEquals($category->id, $result[0]['id']);
        $this->assertEquals($category->name, $result[0]['name']);
    }

    public function testGetCategoriesLanIdExist()
    {
        $badLanId = -1;
        try {
            $this->contributionService->getCategories($badLanId);
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
            $this->contributionService->getCategories($badLanId);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}
