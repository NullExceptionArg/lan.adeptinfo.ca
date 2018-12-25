<?php

namespace Tests\Unit\Repository\Role;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class FindGlobalRoleByIdTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $globalRole;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->globalRole = factory('App\Model\GlobalRole')->create();
    }

    public function testFindGlobalRoleById(): void
    {
        $result = $this->roleRepository->findGlobalRoleById(
            $this->globalRole->id
        );

        $this->assertEquals($this->globalRole->name, $result->name);
        $this->assertEquals($this->globalRole->en_display_name, $result->en_display_name);
        $this->assertEquals($this->globalRole->en_description, $result->en_description);
        $this->assertEquals($this->globalRole->fr_display_name, $result->fr_display_name);
        $this->assertEquals($this->globalRole->fr_description, $result->fr_description);
    }
}
