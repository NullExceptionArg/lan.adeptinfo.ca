<?php

namespace Tests\Unit\Service\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetAllLanTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');
    }

    public function testGetAllLan()
    {
        $lan1 = factory('App\Model\Lan')->create();
        $lan2 = factory('App\Model\Lan')->create();
        $result = $this->lanService->getAllLan();

        $this->assertEquals($lan1->id, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($lan1->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals(date('F Y', strtotime($lan1->lan_start)), $result[0]->jsonSerialize()['date']);
        $this->assertEquals($lan2->id, $result[1]->jsonSerialize()['id']);
        $this->assertEquals($lan2->name, $result[1]->jsonSerialize()['name']);
        $this->assertEquals(date('F Y', strtotime($lan2->lan_start)), $result[1]->jsonSerialize()['date']);
    }
}
