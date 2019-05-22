<?php

namespace Tests\Unit\Repository\Role;

use App\Model\LanRole;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddDefaultLanRolesTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testAddDefaultLanRoles(): void
    {
        $this->assertEquals(0, LanRole::all()->count());

        $this->roleRepository->addDefaultLanRoles($this->lan->id);

        $lanRoles = (include(base_path().'/resources/roles.php'))['lan_roles'];
        $this->assertEquals(count($lanRoles), LanRole::all()->count());
    }
}
