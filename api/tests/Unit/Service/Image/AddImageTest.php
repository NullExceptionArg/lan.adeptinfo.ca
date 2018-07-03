<?php

namespace Tests\Unit\Service\Image;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class AddImageTest extends TestCase
{
    use DatabaseMigrations;

    protected $imageService;

    protected $lan;

    protected $paramsContent = [
        'image' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->imageService = $this->app->make('App\Services\Implementation\ImageServiceImpl');

        $this->lan = factory('App\Model\Lan')->create();

        $this->paramsContent['image'] = factory('App\Model\Image')->make([
            'lan_id' => $this->lan->id
        ])->image;
    }

    public function testAddImage(): void
    {
        $request = new Request($this->paramsContent);
        $result = $this->imageService->addImage($request, $this->lan->id);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals($this->paramsContent['image'], $result['image']);
        $this->assertEquals($this->lan->id, $result['lan_id']);
    }

    public function testAddImageLanIdExists(): void
    {
        $badLanId = -1;
        $request = new Request($this->paramsContent);
        try {
            $this->imageService->addImage($request, $badLanId);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testAddImageLanIdInteger(): void
    {
        $badLanId = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->imageService->addImage($request, $badLanId);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testAddImageRequired(): void
    {
        $this->paramsContent['image'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->imageService->addImage($request, $this->lan->id);
            $this->fail('Expected: {"image":["The image field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"image":["The image field is required."]}', $e->getMessage());
        }
    }

    public function testAddImageString(): void
    {
        $this->paramsContent['image'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->imageService->addImage($request, $this->lan->id);
            $this->fail('Expected: {"image":["The image must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"image":["The image must be a string."]}', $e->getMessage());
        }
    }
}
