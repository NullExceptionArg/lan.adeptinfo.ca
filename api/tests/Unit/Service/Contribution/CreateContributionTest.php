<?php

namespace Tests\Unit\Service\Contribution;


use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
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
        'lan_id' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->contributorService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->paramsContent['contribution_category_id'] = $this->category->id;

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'create-contribution')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->be($this->user);
    }

    public function testCreateCategoryPermission(): void
    {
        $this->paramsContent['lan_id'] = $this->lan->id;
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testCreateContributionUserFullName(): void
    {
        $this->paramsContent['user_full_name'] = $this->user->getFullName();
        $this->paramsContent['lan_id'] = $this->lan->id;
        $request = new Request($this->paramsContent);
        $result = $this->contributorService->createContribution($request);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals($this->paramsContent['user_full_name'], $result['user_full_name']);
        $this->assertEquals($this->category->id, $this->paramsContent['contribution_category_id']);
    }

    public function testCreateContributionUserEmail(): void
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $this->paramsContent['lan_id'] = $this->lan->id;
        $request = new Request($this->paramsContent);
        $result = $this->contributorService->createContribution($request);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
        $this->assertEquals($this->category->id, $this->paramsContent['contribution_category_id']);

    }

    public function testCreateContributionLanIdExist(): void
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $this->paramsContent['lan_id'] = -1;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateContributionLanIdInteger(): void
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $this->paramsContent['lan_id'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateContributionCategoryIdRequired(): void
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $this->paramsContent['contribution_category_id'] = null;
        $this->paramsContent['lan_id'] = $this->lan->id;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request);
            $this->fail('Expected: {"contribution_category_id":["The contribution category id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_category_id":["The contribution category id field is required."]}', $e->getMessage());
        }
    }

    public function testCreateContributionCategoryIdInteger(): void
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $this->paramsContent['contribution_category_id'] = '☭';
        $this->paramsContent['lan_id'] = $this->lan->id;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request);
            $this->fail('Expected: {"contribution_category_id":["The contribution category id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_category_id":["The contribution category id must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateContributionCategoryIdExist(): void
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $this->paramsContent['contribution_category_id'] = -1;
        $this->paramsContent['lan_id'] = $this->lan->id;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request);
            $this->fail('Expected: {"contribution_category_id":["The selected contribution category id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"contribution_category_id":["The selected contribution category id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateContributionUserFullNameString(): void
    {
        $this->paramsContent['user_full_name'] = 1;
        $this->paramsContent['lan_id'] = $this->lan->id;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request);
            $this->fail('Expected: {"user_full_name":["The user full name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_full_name":["The user full name must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateContributionUserEmailString(): void
    {
        $this->paramsContent['user_email'] = 1;
        $this->paramsContent['lan_id'] = $this->lan->id;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request);
            $this->fail('Expected: {"user_email":["The user email must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_email":["The user email must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateContributionUserFullNameOrUserEmailNotNull(): void
    {
        $this->paramsContent['lan_id'] = $this->lan->id;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request);
            $this->fail('Expected: {"user_full_name":["The user full name field is required when user email is not present."],"user_email":["The user email field is required when user full name is not present."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_full_name":["The user full name field is required when user email is not present."],"user_email":["The user email field is required when user full name is not present."]}', $e->getMessage());
        }
    }

    public function testCreateContributionUserEmailAndUserFullNameNotFilled(): void
    {
        $this->paramsContent['user_email'] = $this->user->email;
        $this->paramsContent['user_full_name'] = $this->user->getFullName();
        $this->paramsContent['lan_id'] = $this->lan->id;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createContribution($request);
            $this->fail('Expected: {"user_full_name":["Field user full name can\'t be used if the field user_email is used too."],"user_email":["Field user email can\'t be used if the field user_full_name is used too."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_full_name":["Field user full name can\'t be used if the field user_email is used too."],"user_email":["Field user email can\'t be used if the field user_full_name is used too."]}', $e->getMessage());
        }
    }
}
