<?php

namespace Tests\Unit\Repository\Role;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class LinkGlobalRoleUserTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $user;
    protected $globalRole;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->globalRole = factory('App\Model\GlobalRole')->create();
    }

    public function testLinkGlobalRoleUser(): void
    {
        $this->notSeeInDatabase('global_role_user', [
            'user_id' => $this->user->id,
            'role_id' => $this->globalRole->id,
        ]);

        $this->roleRepository->linkGlobalRoleUser(
            $this->globalRole->id,
            $this->user->id
        );

        $this->seeInDatabase('global_role_user', [
            'user_id' => $this->user->id,
            'role_id' => $this->globalRole->id,
        ]);
    }
}
