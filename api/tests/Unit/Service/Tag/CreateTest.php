<?php

namespace Tests\Unit\Service\Tag;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseMigrations;

    protected $tagService;

    protected $user;

    protected $requestContent = [
        'name' => 'PRO'
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->tagService =  $this->app->make('App\Services\Implementation\TagServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->be($this->user);
    }

    public function testCreate(): void
    {
        $request = new Request($this->requestContent);
        $result = $this->tagService->create($request);

        $this->assertEquals(1, $result->id);
        $this->assertEquals($this->requestContent['name'], $result->name);
    }

    public function testCreateRequired(): void
    {
        $this->requestContent['name'] = null;
        $request = new Request($this->requestContent);
        try {
            $this->tagService->create($request);
            $this->fail('Expected: {"name":["The name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateString(): void
    {
        $this->requestContent['name'] = 1;
        $request = new Request($this->requestContent);
        try {
            $this->tagService->create($request);
            $this->fail('Expected: {"name":["The name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateMaxLength(): void
    {
        $this->requestContent['name'] = str_repeat('☭', 6);
        $request = new Request($this->requestContent);
        try {
            $this->tagService->create($request);
            $this->fail('Expected: {"name":["The name may not be greater than 5 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name may not be greater than 5 characters."]}', $e->getMessage());
        }
    }

    public function testCreateUnique(): void
    {
        factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
            'name' => $this->requestContent['name']
        ]);
        $request = new Request($this->requestContent);
        try {
            $this->tagService->create($request);
            $this->fail('Expected: {"name":["The name has already been taken."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name has already been taken."]}', $e->getMessage());
        }
    }

}
