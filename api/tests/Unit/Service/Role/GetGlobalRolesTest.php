<?php

namespace Tests\Unit\Service\Role;

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
    }

    public function testGetGlobalRoles(): void
    {
        $result = $this->roleService->getGlobalRoles();

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
    }
}
