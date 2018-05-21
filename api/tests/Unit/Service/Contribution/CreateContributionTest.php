<?php

namespace Tests\Unit\Service\Contribution;


use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class CreateContributionTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributorService;

    protected $user;
    protected $lan;
    protected $category;

    protected $paramsContent = [
        'contribution_category_id' => null,
        'user_full_name' => null,
        'user_email' => null,
    ];

    public function setUp()
    {
        parent::setUp();
        $this->contributorService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->paramsContent['contribution_category_id'] = $this->category->id;
    }


    public function testCreateContributionUserFullName()
    {
        $this->paramsContent['user_full_name'] = $this->user->getFullName();
        $request = new Request($this->paramsContent);
        $result = $this->contributorService->createContribution($request, $this->lan->id);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals($this->paramsContent['user_full_name'], $result['user_full_name']);
        $this->assertEquals($this->category->id, $this->paramsContent['contribution_category_id']);
    }

    public function testCreateContributionUserEmail()
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $request = new Request($this->paramsContent);
        $result = $this->contributorService->createContribution($request, $this->lan->id);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
        $this->assertEquals($this->category->id, $this->paramsContent['contribution_category_id']);

    }

    public function testCreateContributionLanIdExist()
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $badLanId = -1;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request, $badLanId);
            $this->fail('Expected: {"lan_id":["Lan with id ' . $badLanId . ' doesn\'t exist"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateContributionLanIdInteger()
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $badLanId = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request, $badLanId);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateContributionCategoryIdRequired()
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $this->paramsContent['contribution_category_id'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request, $this->lan->id);
            $this->fail('Expected: {"contribution_category_id":["The contribution category id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_category_id":["The contribution category id field is required."]}', $e->getMessage());
        }
    }

    public function testCreateContributionCategoryIdInteger()
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $this->paramsContent['contribution_category_id'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request, $this->lan->id);
            $this->fail('Expected: {"contribution_category_id":["The contribution category id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_category_id":["The contribution category id must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateContributionCategoryIdExist()
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $this->paramsContent['contribution_category_id'] = -1;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request, $this->lan->id);
            $this->fail('Expected: {"contribution_category_id":["The selected contribution category id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_category_id":["The selected contribution category id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateContributionUserFullNameString()
    {
        $this->paramsContent['user_full_name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request, $this->lan->id);
            $this->fail('Expected: {"user_full_name":["The user full name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_full_name":["The user full name must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateContributionUserEmailString()
    {
        $this->paramsContent['user_email'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request, $this->lan->id);
            $this->fail('Expected: {"user_email":["The user email must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_email":["The user email must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateContributionUserFullNameOrUserEmailNotNull()
    {
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request, $this->lan->id);
            $this->fail('Expected: {"user_full_name":["The user full name field is required when user email is not present."],"user_email":["The user email field is required when user full name is not present."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_full_name":["The user full name field is required when user email is not present."],"user_email":["The user email field is required when user full name is not present."]}', $e->getMessage());
        }
    }

    public function testCreateContributionUserEmailAndUserFullNameNotFilled()
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $this->paramsContent['user_full_name'] = $this->user->getFullName();
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request, $this->lan->id);
            $this->fail('Expected: {"user_full_name":["Field can\'t be used if user_email is used too."],"user_email":["Field can\'t be used if user_full_name is used too."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_full_name":["Field can\'t be used if user_email is used too."],"user_email":["Field can\'t be used if user_full_name is used too."]}', $e->getMessage());
        }
    }
}
