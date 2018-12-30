<?php

namespace Tests\Unit\Controller\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetAllTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetAll(): void
    {
        $lan1 = factory('App\Model\Lan')->create();
        $lan2 = factory('App\Model\Lan')->create();
        $this->json('GET', '/api/lan/all')
            ->seeJsonEquals([
                [
                    'id' => $lan1->id,
                    'name' => $lan1->name,
                    'date' => date('F Y', strtotime($lan1->lan_start)),

                ],
                [
                    'id' => $lan2->id,
                    'name' => $lan2->name,
                    'date' => date('F Y', strtotime($lan2->lan_start)),

                ]
            ])
            ->assertResponseStatus(200);
    }

    public function testGetAllNoLan(): void
    {
        $this->json('GET', '/api/lan/all')
            ->seeJsonEquals([])
            ->assertResponseStatus(200);
    }
}
