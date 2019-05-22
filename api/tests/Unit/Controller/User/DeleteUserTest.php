<?php

namespace Tests\Unit\Controller\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use DatabaseMigrations;

    public function testDeleteUser(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);

        $this->json('DELETE', 'http://'.env('API_DOMAIN').'/user')
            ->seeJsonEquals([])
            ->assertResponseStatus(200);
    }
}
