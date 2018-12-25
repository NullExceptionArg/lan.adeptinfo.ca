<?php

namespace Tests\Unit\Repository\Role;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetPermissionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');
    }

    public function testGetPermissions(): void
    {
        $result = $this->roleRepository->getPermissions();
        $arrayResults = $result->jsonSerialize();
        $permissions = include(base_path() . '/resources/permissions.php');
        for ($i = 0; $i < count($permissions); $i++) {
            $this->assertNotNull($arrayResults[$i]['id']);
            $this->assertEquals($permissions[$i]['name'], $arrayResults[$i]['name']);
            $this->assertEquals($permissions[$i]['can_be_per_lan'], (bool)$arrayResults[$i]['can_be_per_lan']);
        }
    }
}
