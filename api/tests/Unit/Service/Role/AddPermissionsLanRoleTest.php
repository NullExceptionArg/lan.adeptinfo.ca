<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddPermissionsLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $lanRole;
    protected $permissions;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
        ]);
        $this->permissions = Permission::inRandomOrder()
            ->where('can_be_per_lan', true)
            ->take(5)
            ->pluck('id')
            ->toArray();
    }

    public function testAddPermissionsLanRole(): void
    {
        $result = $this->roleService->addPermissionsLanRole($this->lanRole->id, $this->permissions);

        $this->assertEquals($this->lanRole->name, $result->name);
        $this->assertEquals($this->lanRole->en_display_name, $result->en_display_name);
        $this->assertEquals($this->lanRole->en_description, $result->en_description);
        $this->assertEquals($this->lanRole->fr_display_name, $result->fr_display_name);
        $this->assertEquals($this->lanRole->fr_description, $result->fr_description);
    }
}
