<?php

namespace Tests\Unit\Repository\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetCurrentLanTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');
    }

    public function testGetCurrentLanHasCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        factory('App\Model\Lan')->create();

        $result = $this->lanRepository->getCurrentLan();

        $this->assertEquals($lan->id, $result->id);
        $this->assertEquals($lan->is_current, $result->is_current);
    }

    public function testGetCurrentLanHasNoCurrentLan(): void
    {
        factory('App\Model\Lan')->create();
        factory('App\Model\Lan')->create();

        $result = $this->lanRepository->getCurrentLan();

        $this->assertEquals(null, $result);
    }
}
