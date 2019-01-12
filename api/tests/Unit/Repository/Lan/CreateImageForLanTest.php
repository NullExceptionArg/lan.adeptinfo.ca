<?php

namespace Tests\Unit\Repository\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateImageForLanTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    protected $lan;
    protected $image;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');

        $this->lan = factory('App\Model\Lan')->create();
        $this->image = factory('App\Model\LanImage')->make([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testCreateImageForLan(): void
    {
        $result = $this->lanRepository->createImageForLan($this->lan->id, $this->image->image);

        $this->assertEquals(1, $result);

        $this->seeInDatabase('image', [
            'image' => $this->image->image,
            'lan_id' => $this->lan->id
        ]);
    }
}
