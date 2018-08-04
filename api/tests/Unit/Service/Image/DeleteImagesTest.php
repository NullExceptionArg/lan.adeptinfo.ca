<?php

namespace Tests\Unit\Service\Image;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class DeleteImagesTest extends TestCase
{
    use DatabaseMigrations;

    protected $imageService;
    protected $user;
    protected $lan;
    protected $image;
    protected $image1;
    protected $image2;

    public function setUp(): void
    {
        parent::setUp();
        $this->imageService = $this->app->make('App\Services\Implementation\ImageServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->image = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->image1 = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->image2 = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testDeleteImages(): void
    {
        $request = new Request([
            'images_id' => $this->image1->id . ',' . $this->image2->id,
            'lan_id' => $this->lan->id
        ]);
        $result = $this->imageService->deleteImages($request);

        $this->assertEquals([
            $this->image1->id,
            $this->image2->id
        ], $result);
    }

    public function testDeleteImagesCurrentLan(): void
    {
        factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $image1 = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
        $image2 = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
        $request = new Request([
            'images_id' => $image1->id . ',' . $image2->id
        ]);
        $result = $this->imageService->deleteImages($request);

        $this->assertEquals([
            $image1->id,
            $image2->id
        ], $result);
    }

    public function testDeleteImagesLanIdExists(): void
    {
        $request = new Request([
            'images_id' => $this->image1->id . ',' . $this->image2->id,
            'lan_id' => -1
        ]);
        try {
            $this->imageService->deleteImages($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testDeleteImagesLanIdInteger(): void
    {
        $request = new Request([
            'images_id' => $this->image1->id . ',' . $this->image2->id,
            'lan_id' => '☭'
        ]);
        try {
            $this->imageService->deleteImages($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeleteImagesImagesIdString(): void
    {
        $request = new Request([
            'images_id' => -1,
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->imageService->deleteImages($request);
            $this->fail('Expected: {"images_id":["The images id must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"images_id":["The images id must be a string."]}', $e->getMessage());
        }
    }
}
