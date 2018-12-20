<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class AddPermissionLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $lanRole;
    protected $lan;

    protected $paramsContent = [
        'lan_id' => null,
        'role_id' => null,
        'permissions' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'add-permissions-lan-role')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->paramsContent['lan_id'] = $this->lan->id;
        $this->paramsContent['role_id'] = $this->lanRole->id;
        $this->paramsContent['permissions'] = Permission::inRandomOrder()
            ->where('can_be_per_lan', true)
            ->take(5)
            ->pluck('id')
            ->toArray();

        $this->be($this->user);
    }

    public function testAddPermissionLanRole(): void
    {
        $request = new Request($this->paramsContent);

        $result = $this->roleService->addPermissionsLanRole($request);

        $this->assertEquals($this->lanRole->name, $result->name);
        $this->assertEquals($this->lanRole->en_display_name, $result->en_display_name);
        $this->assertEquals($this->lanRole->en_description, $result->en_description);
        $this->assertEquals($this->lanRole->fr_display_name, $result->fr_display_name);
        $this->assertEquals($this->lanRole->fr_description, $result->fr_description);
    }

    public function testAddPermissionLanRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->addPermissionsLanRole($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testAddPermissionLanRoleIdRequired(): void
    {
        $this->paramsContent['role_id'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->addPermissionsLanRole($request);
            $this->fail('Expected: {"role_id":["The role id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id field is required."]}', $e->getMessage());
        }
    }

    public function testAddPermissionLanRoleIdInteger(): void
    {
        $this->paramsContent['role_id'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->addPermissionsLanRole($request);
            $this->fail('Expected: {"role_id":["The role id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id must be an integer."]}', $e->getMessage());
        }
    }

    public function testAddPermissionLanRolePermissionsRequired(): void
    {
        $this->paramsContent['permissions'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->addPermissionsLanRole($request);
            $this->fail('Expected: {"permissions":["The permissions field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The permissions field is required."]}', $e->getMessage());
        }
    }

    public function testAddPermissionLanRolePermissionsArray(): void
    {
        $this->paramsContent['permissions'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->addPermissionsLanRole($request);
            $this->fail('Expected: {"permissions":["The permissions must be an array."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The permissions must be an array."]}', $e->getMessage());
        }
    }

    public function testAddPermissionLanRolePermissionsArrayOfInteger(): void
    {
        $this->paramsContent['permissions'] = [(string)$this->paramsContent['permissions'][0], $this->paramsContent['permissions'][1]];
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->addPermissionsLanRole($request);
            $this->fail('Expected: {"permissions":["The array must contain only integers."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The array must contain only integers."]}', $e->getMessage());
        }
    }

    public function testAddPermissionLanRolePermissionCanBePerLan(): void
    {
        $permission = Permission::where('can_be_per_lan', false)->first();
        $this->paramsContent['permissions'] = [intval($permission->id)];
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->addPermissionsLanRole($request);
            $this->fail('Expected: {"permissions":["One of the provided permissions cannot be attributed to a LAN role."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["One of the provided permissions cannot be attributed to a LAN role."]}', $e->getMessage());
        }
    }

    public function testAddPermissionLanRolePermissionsElementsInArrayExistInPermission(): void
    {
        $this->paramsContent['permissions'] = [$this->paramsContent['permissions'][0], -1];
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->addPermissionsLanRole($request);
            $this->fail('Expected: {"permissions":["An element of the array is not an existing permission id."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["An element of the array is not an existing permission id."]}', $e->getMessage());
        }
    }

    public function testAddPermissionLanRolePermissionsPermissionsDontBelongToRole(): void
    {
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $this->lanRole->id,
            'permission_id' => $this->paramsContent['permissions'][0]
        ]);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->addPermissionsLanRole($request);
            $this->fail('Expected: {"permissions":["One of the provided permissions is already attributed to this role."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["One of the provided permissions is already attributed to this role."]}', $e->getMessage());
        }
    }
}
