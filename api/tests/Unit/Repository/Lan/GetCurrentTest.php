<?php

namespace Tests\Unit\Repository\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetCurrentTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');
    }

    public function testGetCurrentHasCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        factory('App\Model\Lan')->create();

        $result = $this->lanRepository->getCurrent();

        $this->assertEquals($lan->id, $result->id);
        $this->assertEquals($lan->is_current, $result->is_current);
    }

    public function testGetCurrentHasNoCurrentLan(): void
    {
        factory('App\Model\Lan')->create();
        factory('App\Model\Lan')->create();

        $result = $this->lanRepository->getCurrent();

        $this->assertEquals(null, $result);
    }
}
