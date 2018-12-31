<?php

namespace Tests;

use App\Model\GlobalRole;
use App\Model\LanRole;
use App\Model\Permission;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function setUp()
    {
        parent::setUp();
        $this->artisan('lan:permissions');
    }

    public function addLanPermissionToUser(int $userId, int $lanId, string $permissionName): LanRole
    {
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lanId
        ]);
        $permission = Permission::where('name', $permissionName)->first();

        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $userId
        ]);

        return $role;
    }

    public function addGlobalPermissionToUser(int $userId, string $permissionName): GlobalRole
    {
        $role = factory('App\Model\GlobalRole')->create();
        $permission = Permission::where('name', $permissionName)->first();

        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $userId
        ]);

        return $role;
    }
}
