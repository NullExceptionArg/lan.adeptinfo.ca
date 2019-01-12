<?php

namespace Tests\Unit\Service\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddLanImageTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    protected $lan;
    protected $user;
    protected $image;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');

        $this->lan = factory('App\Model\Lan')->create();
        $this->user = factory('App\Model\User')->create();
        $this->image = factory('App\Model\LanImage')->make([
            'lan_id' => $this->lan->id
        ])->image;
    }

    public function testAddLanImage(): void
    {
        $result = $this->lanService->addLanImage($this->lan->id, $this->image);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals($this->image, $result['image']);
    }
}
