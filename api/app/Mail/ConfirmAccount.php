<?php namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmAccount extends Mailable
{
    use Queueable, SerializesModels;

    /** @var string the address to send the email */
    protected $to_address;

    /** @var float the winnings they won */
    protected $code;

    /**
     * Create a new message instance.
     *
     * @param string $to_address the address to send the email
     * @param string $code the winnings they won
     *
     */
    public function __construct(string $to_address, string $code)
    {
        $this->to_address = $to_address;
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to($this->to_address)
            ->subject('LAN de l\'ADEPT - Confirmation du compte')
            ->view('emails.confirm-account')
            ->with(
                [
                    'code' => $this->code
                ]
            );
    }
}