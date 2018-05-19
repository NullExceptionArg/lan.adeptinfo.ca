<?php

namespace Tests\Unit\Service\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class DeleteCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionService;

    public function setUp()
    {
        parent::setUp();
        $this->contributionService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');
    }

    public function testDeleteCategory()
    {
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);

        $result = $this->contributionService->deleteCategory($lan->id, $category->id);

        $this->assertEquals($category->id, $result['contribution_category_id']);
    }

    public function testDeleteCategoryLanIdExist()
    {
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);
        $badLanId = -1;
        try {
            $this->contributionService->deleteCategory($badLanId, $category->id);
            $this->fail('Expected: {"lan_id":["Lan with id ' . $badLanId . ' doesn\'t exist"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["Lan with id ' . $badLanId . ' doesn\'t exist"]}', $e->getMessage());
        }
    }

    public function testDeleteCategoryLanIdInteger()
    {
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);
        $badLanId = '☭';
        try {
            $this->contributionService->deleteCategory($badLanId, $category->id);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeleteCategoryContributionCategoryIdExist()
    {
        $lan = factory('App\Model\Lan')->create();
        $badCategoryId = -1;
        try {
            $this->contributionService->deleteCategory($lan->id, $badCategoryId);
            $this->fail('Expected: {"contribution_category_id":["Contribution category with id ' . $badCategoryId . ' doesn\'t exist"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_category_id":["Contribution category with id ' . $badCategoryId . ' doesn\'t exist"]}', $e->getMessage());
        }
    }

    public function testDeleteCategoryContributionCategoryIdInteger()
    {
        $lan = factory('App\Model\Lan')->create();
        $badCategoryId = '☭';
        try {
            $this->contributionService->deleteCategory($lan->id, $badCategoryId);
            $this->fail('Expected: {"contribution_category_id":["The contribution category id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_category_id":["The contribution category id must be an integer."]}', $e->getMessage());
        }
    }
}
