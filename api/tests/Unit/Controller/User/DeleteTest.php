<?php

namespace Tests\Unit\Controller\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use DatabaseMigrations;

    public function testDeleteUser()
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);

        $this->json('DELETE', '/api/user')
            ->seeJsonEquals([])
            ->assertResponseStatus(200);
    }
}
