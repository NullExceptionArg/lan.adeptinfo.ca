<?php

namespace Tests\Unit\Service\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetCategoriesTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionService;

    protected $lan;
    protected $category;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testGetCategories(): void
    {
        $result = $this->contributionService->getCategories($this->lan->id);

        $this->assertEquals($this->category->id, $result[0]['id']);
        $this->assertEquals($this->category->name, $result[0]['name']);
    }

    public function testGetCategoriesLanIdExist(): void
    {
        $badLanId = -1;
        try {
            $this->contributionService->getCategories($badLanId);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testGetCategoriesLanIdInteger(): void
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
