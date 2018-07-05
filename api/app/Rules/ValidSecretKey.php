<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioException;

class ValidSecretKey implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $seatsClient = new SeatsioClient($value);
        try {
            $seatsClient->charts()->listAllTags();
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
        return 'The secret key :attribute is not valid.';
    }
}