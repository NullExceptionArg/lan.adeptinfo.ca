<?php

namespace Tests\Unit\Controller\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetLansTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetLans(): void
    {
        $lan1 = factory('App\Model\Lan')->create();
        $lan2 = factory('App\Model\Lan')->create();
        $this->json('GET', '/api/lans')
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

    public function testGetLansNoLan(): void
    {
        $this->json('GET', '/api/lans')
            ->seeJsonEquals([])
            ->assertResponseStatus(200);
    }
}
