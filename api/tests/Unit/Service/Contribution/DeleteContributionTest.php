<?php

namespace Tests\Unit\Service\Contribution;

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
        $result = $this->contributorService->deleteContribution($this->lan->id, $contribution->id);

        $this->assertEquals($contribution->id, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
    }

    public function testDeleteContributionUserFullName(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_full_name' => $this->user->getFullName()
        ]);
        $result = $this->contributorService->deleteContribution($this->lan->id, $contribution->id);

        $this->assertEquals($contribution->id, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
    }

    public function testDeleteContributionLanIdExist(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);
        $badLanId = -1;
        try {
            $this->contributorService->deleteContribution($badLanId, $contribution->id);
            $this->fail('Expected: {"lan_id":["Lan with id ' . $badLanId . ' doesn\'t exist"]}');
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
        $badLanId = '☭';
        try {
            $this->contributorService->deleteContribution($badLanId, $contribution->id);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeleteContributionCategoryIdInteger(): void
    {
        $badContributionId = '☭';
        try {
            $this->contributorService->deleteContribution($this->lan->id, $badContributionId);
            $this->fail('Expected: {"contribution_id":["The contribution id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_id":["The contribution id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeleteContributionCategoryIdExist(): void
    {

        $badContributionId = -1;
        try {
            $this->contributorService->deleteContribution($this->lan->id, $badContributionId);
            $this->fail('Expected: {"contribution_id":["The selected contribution id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_id":["The selected contribution id is invalid."]}', $e->getMessage());
        }
    }
}
