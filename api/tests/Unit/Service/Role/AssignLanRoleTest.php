<?php

namespace Tests\Unit\Service\Role;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AssignLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $role;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->lan = factory('App\Model\Lan')->create();
        $this->user = factory('App\Model\User')->create();
        $this->role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testAssignLanRole(): void
    {
        $result = $this->roleService->assignLanRole($this->role->id, $this->user->email);

        $this->assertEquals($this->role->name, $result->name);
        $this->assertEquals($this->role->en_display_name, $result->en_display_name);
        $this->assertEquals($this->role->en_description, $result->en_description);
        $this->assertEquals($this->role->fr_display_name, $result->fr_display_name);
        $this->assertEquals($this->role->fr_description, $result->fr_description);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }
}
