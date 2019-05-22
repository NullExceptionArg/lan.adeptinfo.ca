<?php

namespace Tests\Unit\Service\Role;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetPermissionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
    }

    public function testGetPermissions(): void
    {
        $result = $this->roleService->getPermissions();
        $arrayResults = $result->collection->jsonSerialize();
        $permissions = include base_path().'/resources/permissions.php';
        for ($i = 0; $i < count($permissions); $i++) {
            $this->assertNotNull($arrayResults[$i]['id']);
            $this->assertEquals($permissions[$i]['name'], $arrayResults[$i]['name']);
            $this->assertEquals($permissions[$i]['can_be_per_lan'], (bool) $arrayResults[$i]['can_be_per_lan']);
            $this->assertEquals(trans('permission.display-name-'.$permissions[$i]['name']), $arrayResults[$i]['display_name']);
            $this->assertEquals(trans('permission.description-'.$permissions[$i]['name']), $arrayResults[$i]['description']);
        }
    }
}
