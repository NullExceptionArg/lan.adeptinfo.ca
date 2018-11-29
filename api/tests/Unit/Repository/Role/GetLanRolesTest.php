<?php

namespace Tests\Unit\Repository\Role;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetLanRolesTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $lan;
    protected $lanRoles;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRoles = factory('App\Model\LanRole', 3)->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testGetLanRoleTest(): void
    {
        $result = $this->roleRepository->getLanRoles($this->lan->id);

        $this->assertEquals($this->lanRoles[0]['name'], $result[0]->jsonSerialize()['name']);
        $this->assertEquals($this->lanRoles[0]['en_display_name'], $result[0]->jsonSerialize()['en_display_name']);
        $this->assertEquals($this->lanRoles[0]['en_description'], $result[0]->jsonSerialize()['en_description']);
        $this->assertEquals($this->lanRoles[0]['fr_display_name'], $result[0]->jsonSerialize()['fr_display_name']);
        $this->assertEquals($this->lanRoles[0]['fr_description'], $result[0]->jsonSerialize()['fr_description']);
        $this->assertEquals($this->lan->id, $result[0]->jsonSerialize()['lan_id']);

        $this->assertEquals($this->lanRoles[1]['name'], $result[1]->jsonSerialize()['name']);
        $this->assertEquals($this->lanRoles[1]['en_display_name'], $result[1]->jsonSerialize()['en_display_name']);
        $this->assertEquals($this->lanRoles[1]['en_description'], $result[1]->jsonSerialize()['en_description']);
        $this->assertEquals($this->lanRoles[1]['fr_display_name'], $result[1]->jsonSerialize()['fr_display_name']);
        $this->assertEquals($this->lanRoles[1]['fr_description'], $result[1]->jsonSerialize()['fr_description']);
        $this->assertEquals($this->lan->id, $result[1]->jsonSerialize()['lan_id']);

        $this->assertEquals($this->lanRoles[2]['name'], $result[2]->jsonSerialize()['name']);
        $this->assertEquals($this->lanRoles[2]['en_display_name'], $result[2]->jsonSerialize()['en_display_name']);
        $this->assertEquals($this->lanRoles[2]['en_description'], $result[2]->jsonSerialize()['en_description']);
        $this->assertEquals($this->lanRoles[2]['fr_display_name'], $result[2]->jsonSerialize()['fr_display_name']);
        $this->assertEquals($this->lanRoles[2]['fr_description'], $result[2]->jsonSerialize()['fr_description']);
        $this->assertEquals($this->lan->id, $result[2]->jsonSerialize()['lan_id']);
    }
}
