<?php

namespace App\Rules;

use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioException;

class ValidEventKey implements Rule
{

    protected $lanId;
    protected $secretKeyId;

    public function __construct(?string $lanId, ?string $secretKeyId)
    {
        $this->lanId = $lanId;
        $this->secretKeyId = $secretKeyId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $seatsClient = null;
        if ($this->secretKeyId == null) {
            if ($this->lanId == null) {
                return false;
            }
            $this->secretKeyId = Lan::find($this->lanId)->secret_key_id;
        }

        $seatsClient = new SeatsioClient($this->secretKeyId);
        try {
            $seatsClient->events()->retrieve($value);
        } catch (SeatsioException $exception) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.valid_event_key');
    }
}