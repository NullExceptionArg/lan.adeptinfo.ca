<?php

namespace Tests\Unit\Service\Role;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetLanRolesTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $lanRoles;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRoles = factory('App\Model\LanRole', 3)->create([
            'lan_id' => $this->lan->id,
        ]);
    }

    public function testGetLanRoles(): void
    {
        $result = $this->roleService->getLanRoles($this->lan->id);

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
    }
}
