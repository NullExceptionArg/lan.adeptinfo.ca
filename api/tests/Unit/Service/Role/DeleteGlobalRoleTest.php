<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class DeleteGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $globalRole;

    protected $paramsContent = [
        'role_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->globalRole = factory('App\Model\GlobalRole')->create();

        $role = factory('App\Model\GlobalRole')->create();
        $permission = Permission::where('name', 'delete-global-role')->first();
        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $permissions = Permission::inRandomOrder()
            ->take(10)
            ->pluck('id')
            ->toArray();

        foreach ($permissions as $permissionId) {
            factory('App\Model\PermissionGlobalRole')->create([
                'role_id' => $this->globalRole->id,
                'permission_id' => $permissionId
            ]);
        }

        factory('App\Model\GlobalRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $this->globalRole->id
        ]);

        $this->paramsContent['role_id'] = $this->globalRole->id;

        $this->be($this->user);
    }

    public function testDeleteGlobalRole(): void
    {
        $request = new Request($this->paramsContent);

        $result = $this->roleService->deleteGlobalRole($request);

        $this->assertEquals($this->globalRole->name, $result->name);
        $this->assertEquals($this->globalRole->en_display_name, $result->en_display_name);
        $this->assertEquals($this->globalRole->en_description, $result->en_description);
        $this->assertEquals($this->globalRole->fr_display_name, $result->fr_display_name);
        $this->assertEquals($this->globalRole->fr_description, $result->fr_description);
    }

    public function testDeleteGlobalRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deleteGlobalRole($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testDeleteGlobalRoleIdRequired(): void
    {
        $this->paramsContent['role_id'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deleteGlobalRole($request);
            $this->fail('Expected: {"role_id":["The role id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id field is required."]}', $e->getMessage());
        }
    }

    public function testDeleteGlobalRoleIdInteger(): void
    {
        $this->paramsContent['role_id'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deleteGlobalRole($request);
            $this->fail('Expected: {"role_id":["The role id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeleteGlobalRoleIdExist(): void
    {
        $this->paramsContent['role_id'] = -1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deleteGlobalRole($request);
            $this->fail('Expected: {"role_id":["The selected role id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The selected role id is invalid."]}', $e->getMessage());
        }
    }
}
