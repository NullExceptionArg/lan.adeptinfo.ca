<?php

namespace Tests\Unit\Controller\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class ConfirmTest extends TestCase
{
    use DatabaseMigrations;

    protected $requestContent = [
        'confirmation_code' => '123456789'
    ];

    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create([
            'confirmation_code' => $this->requestContent['confirmation_code']
        ]);
    }

    public function testConfirm(): void
    {
        $this->json('GET', '/api/user/confirm/' . $this->requestContent['confirmation_code'])
            ->assertResponseStatus(200);
    }

    public function testConfirmConfirmationCodeExist(): void
    {
        $this->requestContent['confirmation_code'] = -1;
        $this->json('GET', '/api/user/confirm/' . $this->requestContent['confirmation_code'])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'confirmation_code' => [
                        0 => 'The selected confirmation code is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
