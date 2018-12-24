<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetGlobalRolesTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $globalRoles;
    protected $accessRole;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->globalRoles = factory('App\Model\GlobalRole', 3)->create();

        $this->accessRole = factory('App\Model\GlobalRole')->create();
        $permission = Permission::where('name', 'get-global-roles')->first();
        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $this->accessRole->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $this->accessRole->id,
            'user_id' => $this->user->id
        ]);

        $this->be($this->user);
    }

    public function testGetGlobalRoles(): void
    {
        $result = $this->roleService->getGlobalRoles(new Request());

        $this->assertEquals($this->globalRoles[0]['id'], $result[0]->jsonSerialize()['id']);
        $this->assertEquals($this->globalRoles[0]['name'], $result[0]->jsonSerialize()['name']);
        $this->assertEquals($this->globalRoles[0]['en_display_name'], $result[0]->jsonSerialize()['display_name']);
        $this->assertEquals($this->globalRoles[0]['en_description'], $result[0]->jsonSerialize()['description']);

        $this->assertEquals($this->globalRoles[1]['id'], $result[1]->jsonSerialize()['id']);
        $this->assertEquals($this->globalRoles[1]['name'], $result[1]->jsonSerialize()['name']);
        $this->assertEquals($this->globalRoles[1]['en_display_name'], $result[1]->jsonSerialize()['display_name']);
        $this->assertEquals($this->globalRoles[1]['en_description'], $result[1]->jsonSerialize()['description']);

        $this->assertEquals($this->globalRoles[2]['id'], $result[2]->jsonSerialize()['id']);
        $this->assertEquals($this->globalRoles[2]['name'], $result[2]->jsonSerialize()['name']);
        $this->assertEquals($this->globalRoles[2]['en_display_name'], $result[2]->jsonSerialize()['display_name']);
        $this->assertEquals($this->globalRoles[2]['en_description'], $result[2]->jsonSerialize()['description']);

        $this->assertEquals($this->accessRole['id'], $result[3]->jsonSerialize()['id']);
        $this->assertEquals($this->accessRole['name'], $result[3]->jsonSerialize()['name']);
        $this->assertEquals($this->accessRole['en_display_name'], $result[3]->jsonSerialize()['display_name']);
        $this->assertEquals($this->accessRole['en_description'], $result[3]->jsonSerialize()['description']);
    }

    public function testGetGlobalRolesLanHasPermission(): void
    {
        $user = $this->user = factory('App\Model\User')->create();
        $this->be($user);
        try {
            $this->roleService->getGlobalRoles(new Request());
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }
}
