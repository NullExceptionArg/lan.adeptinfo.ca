<?php

namespace Tests\Unit\Repository\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class SetCurrentLanTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');
    }

    public function testRemoveCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create();
        factory('App\Model\Lan')->create();

        $this->seeInDatabase('lan', [
            'id' => $lan->id,
            'is_current' => false
        ]);

        $this->lanRepository->setCurrentLan($lan->id);

        $this->seeInDatabase('lan', [
            'id' => $lan->id,
            'is_current' => true
        ]);
    }
}
