<?php

namespace Tests\Unit\Service\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteLanImagesTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;
    protected $user;
    protected $lan;
    protected $image;
    protected $image1;
    protected $image2;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->image = factory('App\Model\LanImage')->create([
            'lan_id' => $this->lan->id,
        ]);
        $this->image1 = factory('App\Model\LanImage')->create([
            'lan_id' => $this->lan->id,
        ]);
        $this->image2 = factory('App\Model\LanImage')->create([
            'lan_id' => $this->lan->id,
        ]);
    }

    public function testDeleteLanImages(): void
    {
        $result = $this->lanService->deleteLanImages($this->image1->id.','.$this->image2->id);

        $this->assertEquals([
            $this->image1->id,
            $this->image2->id,
        ], $result);
    }
}
