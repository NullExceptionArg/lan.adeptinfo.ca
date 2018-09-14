<?php

namespace Tests\Unit\Service\User;

use App\Model\Permission;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetAdminSummaryTest extends TestCase
{
    use DatabaseMigrations;

    protected $userService;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\Implementation\UserServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->be($this->user);
    }

    public function testGetAdminSummary(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $permissions = Permission::inRandomOrder()
            ->take(5)
            ->get();
        $role = factory('App\Model\Role')->create([
            'lan_id' => $lan->id
        ]);
        foreach ($permissions as $permission) {
            factory('App\Model\PermissionRole')->create([
                'permission_id' => $permission->id,
                'role_id' => $role->id
            ]);
        }
        factory('App\Model\RoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $role->id
        ]);

        $request = new Request([
            'lan_id' => $lan->id
        ]);
        $result = $this->userService->getAdminSummary($request)->jsonSerialize();
        $permissionsResult = $result['permissions']->jsonSerialize();

        $this->assertEquals($this->user->first_name, $result['first_name']);
        $this->assertEquals($this->user->last_name, $result['last_name']);

        $this->assertEquals($permissions[0]->id, $permissionsResult[0]['id']);
        $this->assertEquals($permissions[0]->name, $permissionsResult[0]['name']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[0]->name), $permissionsResult[0]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[0]->name), $permissionsResult[0]['description']);

        $this->assertEquals($permissions[1]->id, $permissionsResult[1]['id']);
        $this->assertEquals($permissions[1]->name, $permissionsResult[1]['name']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[1]->name), $permissionsResult[1]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[1]->name), $permissionsResult[1]['description']);

        $this->assertEquals($permissions[2]->id, $permissionsResult[2]['id']);
        $this->assertEquals($permissions[2]->name, $permissionsResult[2]['name']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[2]->name), $permissionsResult[2]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[2]->name), $permissionsResult[2]['description']);

        $this->assertEquals($permissions[3]->id, $permissionsResult[3]['id']);
        $this->assertEquals($permissions[3]->name, $permissionsResult[3]['name']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[3]->name), $permissionsResult[3]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[3]->name), $permissionsResult[3]['description']);

        $this->assertEquals($permissions[4]->id, $permissionsResult[4]['id']);
        $this->assertEquals($permissions[4]->name, $permissionsResult[4]['name']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[4]->name), $permissionsResult[4]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[4]->name), $permissionsResult[4]['description']);
    }

    public function testGetAdminSummaryCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $permissions = Permission::inRandomOrder()
            ->take(5)
            ->get();
        $role = factory('App\Model\Role')->create([
            'lan_id' => $lan->id
        ]);
        foreach ($permissions as $permission) {
            factory('App\Model\PermissionRole')->create([
                'permission_id' => $permission->id,
                'role_id' => $role->id
            ]);
        }
        factory('App\Model\RoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $role->id
        ]);

        $request = new Request();
        $result = $this->userService->getAdminSummary($request)->jsonSerialize();
        $permissionsResult = $result['permissions']->jsonSerialize();

        $this->assertEquals($this->user->first_name, $result['first_name']);
        $this->assertEquals($this->user->last_name, $result['last_name']);

        $this->assertEquals($permissions[0]->id, $permissionsResult[0]['id']);
        $this->assertEquals($permissions[0]->name, $permissionsResult[0]['name']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[0]->name), $permissionsResult[0]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[0]->name), $permissionsResult[0]['description']);

        $this->assertEquals($permissions[1]->id, $permissionsResult[1]['id']);
        $this->assertEquals($permissions[1]->name, $permissionsResult[1]['name']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[1]->name), $permissionsResult[1]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[1]->name), $permissionsResult[1]['description']);

        $this->assertEquals($permissions[2]->id, $permissionsResult[2]['id']);
        $this->assertEquals($permissions[2]->name, $permissionsResult[2]['name']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[2]->name), $permissionsResult[2]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[2]->name), $permissionsResult[2]['description']);

        $this->assertEquals($permissions[3]->id, $permissionsResult[3]['id']);
        $this->assertEquals($permissions[3]->name, $permissionsResult[3]['name']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[3]->name), $permissionsResult[3]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[3]->name), $permissionsResult[3]['description']);

        $this->assertEquals($permissions[4]->id, $permissionsResult[4]['id']);
        $this->assertEquals($permissions[4]->name, $permissionsResult[4]['name']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[4]->name), $permissionsResult[4]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[4]->name), $permissionsResult[4]['description']);
    }

    public function testGetAdminSummaryLanIdExist(): void
    {
        $request = new Request([
            'lan_id' => -1
        ]);
        try {
            $this->userService->getAdminSummary($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testGetAdminSummaryLanIdInteger(): void
    {
        $request = new Request([
            'lan_id' => '☭'
        ]);
        try {
            $this->userService->getAdminSummary($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}
