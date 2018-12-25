<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetLanRolesTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $lanRoles;
    protected $lan;
    protected $accessRole;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRoles = factory('App\Model\LanRole', 3)->create([
            'lan_id' => $this->lan->id
        ]);

        $this->accessRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'get-lan-roles')->first();
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

    public function testGetLanRoles(): void
    {
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        $result = $this->roleService->getLanRoles($request);

        $this->assertEquals($this->lanRoles[0]['id'], $result[0]->jsonSerialize()['id']);
        $this->assertEquals($this->lanRoles[0]['name'], $result[0]->jsonSerialize()['name']);
        $this->assertEquals($this->lanRoles[0]['en_display_name'], $result[0]->jsonSerialize()['display_name']);
        $this->assertEquals($this->lanRoles[0]['en_description'], $result[0]->jsonSerialize()['description']);

        $this->assertEquals($this->lanRoles[1]['id'], $result[1]->jsonSerialize()['id']);
        $this->assertEquals($this->lanRoles[1]['name'], $result[1]->jsonSerialize()['name']);
        $this->assertEquals($this->lanRoles[1]['en_display_name'], $result[1]->jsonSerialize()['display_name']);
        $this->assertEquals($this->lanRoles[1]['en_description'], $result[1]->jsonSerialize()['description']);

        $this->assertEquals($this->lanRoles[2]['id'], $result[2]->jsonSerialize()['id']);
        $this->assertEquals($this->lanRoles[2]['name'], $result[2]->jsonSerialize()['name']);
        $this->assertEquals($this->lanRoles[2]['en_display_name'], $result[2]->jsonSerialize()['display_name']);
        $this->assertEquals($this->lanRoles[2]['en_description'], $result[2]->jsonSerialize()['description']);

        $this->assertEquals($this->accessRole['id'], $result[3]->jsonSerialize()['id']);
        $this->assertEquals($this->accessRole['name'], $result[3]->jsonSerialize()['name']);
        $this->assertEquals($this->accessRole['en_display_name'], $result[3]->jsonSerialize()['display_name']);
        $this->assertEquals($this->accessRole['en_description'], $result[3]->jsonSerialize()['description']);
    }

    public function testGetLanRolesLanHasPermission(): void
    {
        $user = $this->user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request(['lan_id' => $this->lan->id]);
        try {
            $this->roleService->getLanRoles($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testCreateLanRoleLanIdExists(): void
    {
        $request = new Request(['lan_id' => -1]);
        try {
            $this->roleService->getLanRoles($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleLanIdInteger(): void
    {
        $request = new Request(['lan_id' => '☭']);
        try {
            $this->roleService->getLanRoles($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}
