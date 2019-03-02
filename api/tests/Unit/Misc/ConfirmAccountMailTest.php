<?php

namespace Tests\Unit\Misc;

use App\Mail\ConfirmAccount;
use Illuminate\Support\Facades\Mail;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class ConfirmAccountMailTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
    }

    public function testConfirmAccount()
    {
        Mail::fake();

        $confirmationCode = str_random(30);
        Mail::send(
            (new ConfirmAccount($this->user->email, $confirmationCode, $this->user->first_name))->build()
        );

        Mail::assertSent(ConfirmAccount::class, function ($mail) use ($confirmationCode) {
            return $mail->hasTo($this->user->email) &&
                $mail->subject === 'LAN de l\'ADEPT - Confirmation du compte' &&
                $mail->viewData['code'] === $confirmationCode &&
                $mail->viewData['name'] === $this->user->first_name;
        });
    }
}
