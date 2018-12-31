<?php

namespace Tests\Unit\Service\Role;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AssignGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $role;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->role = factory('App\Model\GlobalRole')->create();

        $this->be($this->user);
    }

    public function testAssignGlobalRole(): void
    {
        $result = $this->roleService->assignGlobalRole($this->role->id, $this->user->email);

        $this->assertEquals($this->role->name, $result->name);
        $this->assertEquals($this->role->en_display_name, $result->en_display_name);
        $this->assertEquals($this->role->en_description, $result->en_description);
        $this->assertEquals($this->role->fr_display_name, $result->fr_display_name);
        $this->assertEquals($this->role->fr_description, $result->fr_description);
    }
}
