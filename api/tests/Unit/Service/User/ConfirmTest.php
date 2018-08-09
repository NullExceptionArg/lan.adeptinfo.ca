<?php

namespace Tests\Unit\Service\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class ConfirmTest extends TestCase
{
    use DatabaseMigrations;

    protected $userService;

    protected $paramsContent = [
        'confirmation_code' => '123456789'
    ];

    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\Implementation\UserServiceImpl');

        $this->user = factory('App\Model\User')->create([
            'confirmation_code' => $this->paramsContent['confirmation_code']
        ]);
    }

    public function testConfirm(): void
    {
        $this->seeInDatabase('user', [
            'id' => $this->user->id,
            'is_confirmed' => false
        ]);

        $this->userService->confirm($this->paramsContent['confirmation_code']);

        $this->seeInDatabase('user', [
            'id' => $this->user->id,
            'is_confirmed' => true,
            'confirmation_code' => null
        ]);
    }

    public function testConfirmConfirmationCodeExist(): void
    {
        $this->paramsContent['confirmation_code'] = -1;
        try {
            $this->userService->confirm($this->paramsContent['confirmation_code']);
            $this->fail('Expected: {"confirmation_code":["The selected confirmation code is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"confirmation_code":["The selected confirmation code is invalid."]}', $e->getMessage());
        }
    }
}
