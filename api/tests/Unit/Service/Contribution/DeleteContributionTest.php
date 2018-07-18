<?php

namespace Tests\Unit\Service\Contribution;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class DeleteContributionTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributorService;

    protected $user;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributorService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testDeleteContributionUserEmail(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);
        $request = new Request([
            'lan_id' => $this->lan->id,
            'contribution_id' => $contribution->id
        ]);
        $result = $this->contributorService->deleteContribution($request);

        $this->assertEquals($contribution->id, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
    }

    public function testDeleteContributionUserFullName(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_full_name' => $this->user->getFullName()
        ]);
        $request = new Request([
            'lan_id' => $this->lan->id,
            'contribution_id' => $contribution->id
        ]);
        $result = $this->contributorService->deleteContribution($request);

        $this->assertEquals($contribution->id, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
    }

    public function testDeleteContributionCurrentLan(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_full_name' => $this->user->getFullName()
        ]);
        factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $request = new Request([
            'contribution_id' => $contribution->id
        ]);
        $result = $this->contributorService->deleteContribution($request);

        $this->assertEquals($contribution->id, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
    }

    public function testDeleteContributionLanIdExist(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);
        $request = new Request([
            'lan_id' => -1,
            'contribution_id' => $contribution->id
        ]);
        try {
            $this->contributorService->deleteContribution($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testDeleteContributionLanIdInteger(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);
        $request = new Request([
            'lan_id' => '☭',
            'contribution_id' => $contribution->id
        ]);
        try {
            $this->contributorService->deleteContribution($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeleteContributionCategoryIdInteger(): void
    {
        $request = new Request([
            'lan_id' => $this->lan->id,
            'contribution_id' => '☭'
        ]);
        try {
            $this->contributorService->deleteContribution($request);
            $this->fail('Expected: {"contribution_id":["The contribution id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_id":["The contribution id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeleteContributionCategoryIdExist(): void
    {
        $request = new Request([
            'lan_id' => $this->lan->id,
            'contribution_id' => -1
        ]);
        try {
            $this->contributorService->deleteContribution($request);
            $this->fail('Expected: {"contribution_id":["The selected contribution id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_id":["The selected contribution id is invalid."]}', $e->getMessage());
        }
    }
}
