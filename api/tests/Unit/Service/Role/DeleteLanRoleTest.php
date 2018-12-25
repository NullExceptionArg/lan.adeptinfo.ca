<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class DeleteLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $lanRole;
    protected $lan;

    protected $paramsContent = [
        'role_id' => null,
        'lan_id' => null
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
        $permission = Permission::where('name', 'delete-lan-role')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $permissions = Permission::inRandomOrder()
            ->where('can_be_per_lan', true)
            ->take(5)
            ->pluck('id')
            ->toArray();

        foreach ($permissions as $permissionId) {
            factory('App\Model\PermissionLanRole')->create([
                'role_id' => $this->lanRole->id,
                'permission_id' => $permissionId
            ]);
        }

        factory('App\Model\LanRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $this->lanRole->id
        ]);

        $this->paramsContent['role_id'] = $this->lanRole->id;
        $this->paramsContent['lan_id'] = $this->lan->id;

        $this->be($this->user);
    }

    public function testDeleteLanRole(): void
    {
        $request = new Request($this->paramsContent);

        $result = $this->roleService->deleteLanRole($request);

        $this->assertEquals($this->lanRole->name, $result->name);
        $this->assertEquals($this->lanRole->en_display_name, $result->en_display_name);
        $this->assertEquals($this->lanRole->en_description, $result->en_description);
        $this->assertEquals($this->lanRole->fr_display_name, $result->fr_display_name);
        $this->assertEquals($this->lanRole->fr_description, $result->fr_description);
    }

    public function testDeleteLanRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deleteLanRole($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testDeleteLanRoleIdRequired(): void
    {
        $this->paramsContent['role_id'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deleteLanRole($request);
            $this->fail('Expected: {"role_id":["The role id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id field is required."]}', $e->getMessage());
        }
    }

    public function testDeleteLanRoleIdInteger(): void
    {
        $this->paramsContent['role_id'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deleteLanRole($request);
            $this->fail('Expected: {"role_id":["The role id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeleteLanRoleIdExist(): void
    {
        $this->paramsContent['role_id'] = -1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deleteLanRole($request);
            $this->fail('Expected: {"role_id":["The selected role id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The selected role id is invalid."]}', $e->getMessage());
        }
    }

    public function testDeleteLanRoleLanIdExist(): void
    {
        $this->paramsContent['lan_id'] = -1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deleteLanRole($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testDeleteLanRoleLanIdRequired(): void
    {
        $this->paramsContent['lan_id'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deleteLanRole($request);
            $this->fail('Expected: {"lan_id":["The lan id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id field is required."]}', $e->getMessage());
        }
    }

    public function testDeleteLanRoleLanIdInteger(): void
    {
        $this->paramsContent['lan_id'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deleteLanRole($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}
