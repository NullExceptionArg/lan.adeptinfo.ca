<?php

namespace Tests\Unit\Repository\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetImagesForLanTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    protected $lan;
    protected $image1;
    protected $image2;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');

        $this->lan = factory('App\Model\Lan')->create();
        $this->image1 = factory('App\Model\LanImage')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->image2 = factory('App\Model\LanImage')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testFindImageById(): void
    {
        $images = $this->lanRepository->getImagesForLan($this->lan->id);

        $this->assertEquals($this->image1->id, $images[0]->id);
        $this->assertEquals($this->image1->image, $images[0]->image);
        $this->assertEquals($this->image1->lan_id, $images[0]->lan_id);

        $this->assertEquals($this->image2->id, $images[1]->id);
        $this->assertEquals($this->image2->image, $images[1]->image);
        $this->assertEquals($this->image2->lan_id, $images[1]->lan_id);
    }
}
