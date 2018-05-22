<?php

namespace Tests\Unit\Repository\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class FindLanByIdTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    protected $lan;

    public function setUp()
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testFindLanById()
    {
        $foundLan = $this->lanRepository->findLanById($this->lan->id);

        $this->assertEquals($this->lan->id, $foundLan->id);
    }
}
