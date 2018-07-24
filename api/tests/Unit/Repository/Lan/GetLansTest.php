<?php

namespace Tests\Unit\Repository\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetLansTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');
    }

    public function testFindLanById(): void
    {
        $lan1 = factory('App\Model\Lan')->create();
        $lan2 = factory('App\Model\Lan')->create();
        $result = $this->lanRepository->getAllLan();

        $this->assertEquals($lan1->id, $result[0]->id);
        $this->assertEquals($lan1->name, $result[0]->name);
        $this->assertEquals($lan2->id, $result[1]->id);
        $this->assertEquals($lan2->name, $result[1]->name);
    }
}
