<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class AssignLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $role;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->lan = factory('App\Model\Lan')->create();
        $this->user = factory('App\Model\User')->create();
        $this->role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'assign-lan-role')->first();
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

    public function testAssignLanRole(): void
    {
        $request = new Request([
            'role_id' => $this->role->id,
            'email' => $this->user->email,
            'lan_id' => $this->lan->id
        ]);
        $result = $this->roleService->assignLanRole($request);

        $this->assertEquals($this->role->name, $result->name);
        $this->assertEquals($this->role->en_display_name, $result->en_display_name);
        $this->assertEquals($this->role->en_description, $result->en_description);
        $this->assertEquals($this->role->fr_display_name, $result->fr_display_name);
        $this->assertEquals($this->role->fr_description, $result->fr_description);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }

    public function testAssignLanRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request([
            'role_id' => $this->role->id,
            'email' => $this->user->email,
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->roleService->assignLanRole($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testAssignLanRoleEmailRequired(): void
    {
        $request = new Request([
            'role_id' => $this->role->id,
            'lan_id' => $this->lan->id,
        ]);
        try {
            $this->roleService->assignLanRole($request);
            $this->fail('Expected: {"email":["The email field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"email":["The email field is required."]}', $e->getMessage());
        }
    }

    public function testAssignLanRoleEmailExist(): void
    {
        $request = new Request([
            'email' => '☭',
            'role_id' => $this->role->id,
            'lan_id' => $this->lan->id,
        ]);
        try {
            $this->roleService->assignLanRole($request);
            $this->fail('Expected: {"email":["The selected email is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"email":["The selected email is invalid."]}', $e->getMessage());
        }
    }

    public function testAssignLanRoleIdInteger(): void
    {
        $request = new Request([
            'role_id' => '☭',
            'email' => $this->user->email,
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->roleService->assignLanRole($request);
            $this->fail('Expected: {"role_id":["The role id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id must be an integer."]}', $e->getMessage());
        }
    }

    public function testAssignGlobalRoleIdLanRoleOncePerUser(): void
    {
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $this->role->id,
            'user_id' => $this->user->id
        ]);
        $request = new Request([
            'role_id' => $this->role->id,
            'email' => $this->user->email,
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->roleService->assignLanRole($request);
            $this->fail('Expected: {"role_id":["The user already has this role."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The user already has this role."]}', $e->getMessage());
        }
    }

    public function testAssignLanRoleIdExist(): void
    {
        $request = new Request([
            'role_id' => -1,
            'email' => $this->user->email,
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->roleService->assignLanRole($request);
            $this->fail('Expected: {"role_id":["The selected role id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The selected role id is invalid."]}', $e->getMessage());
        }
    }
}