<?php

namespace Tests\Unit\Service\Contribution;

use App\Model\Contribution;
use App\Model\ContributionCategory;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class DeleteCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionService;

    protected $user;
    protected $lan;
    protected $category;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testDeleteCategorySimple(): void
    {
        $request = new Request([
           'lan_id' => $this->lan->id,
            'contribution_category_id' => $this->category->id
        ]);
        $result = $this->contributionService->deleteCategory($request);

        $this->assertEquals($this->category->id, $result['id']);
    }

    // Should be updated every time Contribution Category has a new relation
    public function testDeleteContributionCategoryComplexOnCategoryForContribution(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);

        ///Building relations
        // Contribution - Contribution category relation
        $contribution->ContributionCategory()->attach($this->category);

        /// Make sure every relations exist
        // Lan - Contribution
        $this->assertEquals(1, $this->user->Contribution()->count());

        // Contribution - Contribution category
        $this->assertEquals(1, $contribution->ContributionCategory()->count());

        //Contribution category - Lan
        $this->assertEquals(1, $this->category->Lan()->count());

        $request = new Request([
            'lan_id' => $this->lan->id,
            'contribution_category_id' => $this->category->id
        ]);

        $this->contributionService->deleteCategory($request);

        /// Verify relations have been removed
        // Contribution category
        $this->assertEquals(0, ContributionCategory::all()->count());

        // Contribution
        $this->assertEquals(0, Contribution::all()->count());
    }

    // Should be updated every time Contribution Category has a new relation
    public function testDeleteContributionCategoryComplexManyCategoryForContribution(): void
    {
        $category2 = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);

        ///Building relations
        // Contribution - Contribution category relation
        $contribution->ContributionCategory()->attach($this->category);
        $contribution->ContributionCategory()->attach($category2);

        /// Make sure every relations exist
        // Lan - Contribution
        $this->assertEquals(1, $this->user->Contribution()->count());

        // Contribution - Contribution category
        $this->assertEquals(2, $contribution->ContributionCategory()->count());

        //Contribution category - Lan
        $this->assertEquals(1, $this->category->Lan()->count());

        $request = new Request([
            'lan_id' => $this->lan->id,
            'contribution_category_id' => $this->category->id
        ]);

        $this->contributionService->deleteCategory($request);

        /// Verify relations have been removed
        // Contribution category
        $this->assertEquals(1, ContributionCategory::all()->count());

        // Contribution
        $this->assertEquals(1, Contribution::all()->count());
    }

    public function testDeleteCategoryLanIdExist(): void
    {
        $request = new Request([
            'lan_id' => -1,
            'contribution_category_id' => $this->category->id
        ]);
        try {
            $this->contributionService->deleteCategory($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testDeleteCategoryLanIdInteger(): void
    {
        $request = new Request([
            'lan_id' => '☭',
            'contribution_category_id' => $this->category->id
        ]);
        try {
            $this->contributionService->deleteCategory($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeleteCategoryContributionCategoryIdExist(): void
    {
        $request = new Request([
            'lan_id' => $this->lan->id,
            'contribution_category_id' => -1
        ]);
        try {
            $this->contributionService->deleteCategory($request);
            $this->fail('Expected: {"contribution_category_id":["Contribution category with id ' . $badCategoryId . ' doesn\'t exist"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_category_id":["The selected contribution category id is invalid."]}', $e->getMessage());
        }
    }

    public function testDeleteCategoryContributionCategoryIdInteger(): void
    {
        $request = new Request([
            'lan_id' => $this->lan->id,
            'contribution_category_id' => '☭'
        ]);
        try {
            $this->contributionService->deleteCategory($request);
            $this->fail('Expected: {"contribution_category_id":["The contribution category id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_category_id":["The contribution category id must be an integer."]}', $e->getMessage());
        }
    }
}
