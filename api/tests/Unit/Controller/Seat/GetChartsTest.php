<?php

namespace Tests\Unit\Controller\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetChartsTest extends TestCase
{
    use DatabaseMigrations;

    protected $admin;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->addGlobalPermissionToUser(
            $this->admin->id,
            'get-seat-charts'
        );
    }

    public function testGetSeatCharts(): void
    {
        $this->actingAs($this->admin)
            ->json('GET', 'http://'.env('API_DOMAIN').'/seat/charts')
            ->seeJsonStructure([
                'items',
            ])
            ->assertResponseStatus(200);
    }

    public function testGetSeatChartsHasPermission(): void
    {
        $admin = factory('App\Model\User')->create();
        $this->actingAs($admin)
            ->json('GET', 'http://'.env('API_DOMAIN').'/seat/charts')
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }
}
