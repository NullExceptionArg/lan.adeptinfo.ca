<?php

namespace Tests\Unit\Repository\Role;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetGlobalRolesTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $globalRoles;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->globalRoles = factory('App\Model\GlobalRole', 3)->create();
    }

    public function testGetLanRoleTest(): void
    {
        $result = $this->roleRepository->getGlobalRoles();

        $this->assertEquals($this->globalRoles[0]['name'], $result[0]->jsonSerialize()['name']);
        $this->assertEquals($this->globalRoles[0]['en_display_name'], $result[0]->jsonSerialize()['en_display_name']);
        $this->assertEquals($this->globalRoles[0]['en_description'], $result[0]->jsonSerialize()['en_description']);
        $this->assertEquals($this->globalRoles[0]['fr_display_name'], $result[0]->jsonSerialize()['fr_display_name']);
        $this->assertEquals($this->globalRoles[0]['fr_description'], $result[0]->jsonSerialize()['fr_description']);

        $this->assertEquals($this->globalRoles[1]['name'], $result[1]->jsonSerialize()['name']);
        $this->assertEquals($this->globalRoles[1]['en_display_name'], $result[1]->jsonSerialize()['en_display_name']);
        $this->assertEquals($this->globalRoles[1]['en_description'], $result[1]->jsonSerialize()['en_description']);
        $this->assertEquals($this->globalRoles[1]['fr_display_name'], $result[1]->jsonSerialize()['fr_display_name']);
        $this->assertEquals($this->globalRoles[1]['fr_description'], $result[1]->jsonSerialize()['fr_description']);

        $this->assertEquals($this->globalRoles[2]['name'], $result[2]->jsonSerialize()['name']);
        $this->assertEquals($this->globalRoles[2]['en_display_name'], $result[2]->jsonSerialize()['en_display_name']);
        $this->assertEquals($this->globalRoles[2]['en_description'], $result[2]->jsonSerialize()['en_description']);
        $this->assertEquals($this->globalRoles[2]['fr_display_name'], $result[2]->jsonSerialize()['fr_display_name']);
        $this->assertEquals($this->globalRoles[2]['fr_description'], $result[2]->jsonSerialize()['fr_description']);
    }
}
