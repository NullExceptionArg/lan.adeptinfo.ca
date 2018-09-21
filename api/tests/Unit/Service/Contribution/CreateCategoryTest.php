<?php

namespace Tests\Unit\Service\Contribution;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributorService;

    protected $lan;
    protected $user;

    protected $paramsContent = [
        'name' => "Programmer",
        'lan_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->contributorService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');

        $this->lan = factory('App\Model\Lan')->create();
        $this->user = factory('App\Model\User')->create();

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'create-contribution-category')->first();
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

    public function testCreateCategory(): void
    {
        $this->paramsContent['lan_id'] = $this->lan->id;
        $request = new Request($this->paramsContent);
        $result = $this->contributorService->createCategory($request);

        $this->assertEquals($this->paramsContent['name'], $result['name']);
    }

    public function testCreateCategoryPermission(): void
    {
        $this->paramsContent['lan_id'] = $this->lan->id;
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createCategory($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testCreateCategoryLanIdExist(): void
    {
        $this->paramsContent['lan_id'] = -1;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createCategory($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateCategoryLanIdInteger(): void
    {
        $this->paramsContent['lan_id'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createCategory($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateCategoryNameRequired(): void
    {
        $this->paramsContent['lan_id'] = $this->lan->id;
        $this->paramsContent['name'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createCategory($request);
            $this->fail('Expected: {"name":["The name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateCategoryNameString(): void
    {
        $this->paramsContent['lan_id'] = $this->lan->id;
        $this->paramsContent['name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->contributorService->createCategory($request);
            $this->fail('Expected: {"name":["The name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name must be a string."]}', $e->getMessage());
        }
    }
}
