<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetLanUsersTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $lan;
    protected $lanRole;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->accessRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'get-lan-user-roles')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $this->accessRole,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $this->accessRole,
            'user_id' => $this->user->id
        ]);

        $this->be($this->user);
    }

    public function testGetLanUsers(): void
    {
        $users = factory('App\Model\User', 4)->create();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role,
            'permission_id' => $permission->id
        ]);
        foreach ($users as $user) {
            factory('App\Model\LanRoleUser')->create([
                'role_id' => $role,
                'user_id' => $user->id
            ]);
        }

        $request = new Request(['role_id' => $role->id]);
        $result = $this->roleService->getLanUsers($request);

        $this->assertEquals($users[0]['email'], $result[0]->email);
        $this->assertEquals($users[0]['first_name'], $result[0]->first_name);
        $this->assertEquals($users[0]['last_name'], $result[0]->last_name);

        $this->assertEquals($users[1]['email'], $result[1]->email);
        $this->assertEquals($users[1]['first_name'], $result[1]->first_name);
        $this->assertEquals($users[1]['last_name'], $result[1]->last_name);

        $this->assertEquals($users[2]['email'], $result[2]->email);
        $this->assertEquals($users[2]['first_name'], $result[2]->first_name);
        $this->assertEquals($users[2]['last_name'], $result[2]->last_name);

        $this->assertEquals($users[3]['email'], $result[3]->email);
        $this->assertEquals($users[3]['first_name'], $result[3]->first_name);
        $this->assertEquals($users[3]['last_name'], $result[3]->last_name);
    }

    public function testGetLanUsersHasPermission(): void
    {
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $user = $this->user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request(['role_id' => $role->id]);
        try {
            $this->roleService->getLanUsers($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testGetLanUsersRoleIdRequired(): void
    {
        $request = new Request(['role_id' => null]);
        try {
            $this->roleService->getLanUsers($request);
            $this->fail('Expected: {"role_id":["The role id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id field is required."]}', $e->getMessage());
        }
    }

    public function testGetLanUsersRoleIdInteger(): void
    {
        $request = new Request(['role_id' => '☭']);
        try {
            $this->roleService->getLanUsers($request);
            $this->fail('Expected: {"role_id":["The role id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id must be an integer."]}', $e->getMessage());
        }
    }

    public function testGetLanUsersRoleIdExist(): void
    {
        $request = new Request(['role_id' => -1]);
        try {
            $this->roleService->getLanUsers($request);
            $this->fail('Expected: {"role_id":["The selected role id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The selected role id is invalid."]}', $e->getMessage());
        }
    }
}
