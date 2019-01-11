<?php namespace App\Mail;

use Illuminate\{Bus\Queueable, Mail\Mailable, Queue\SerializesModels};

class ConfirmAccount extends Mailable
{
    use Queueable, SerializesModels;

    protected $email;
    protected $code;
    public $name;

    /**
     * Create a new message instance.
     *
     * @param string $email
     * @param string $code
     * @param string $name
     */
    public function __construct(string $email, string $code, string $name)
    {
        $this->email = $email;
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to($this->email)
            ->subject('LAN de l\'ADEPT - Confirmation du compte')
            ->view('emails.confirm-account')
            ->with([
                'code' => $this->code,
                'name' => $this->name
            ]);
    }
}
