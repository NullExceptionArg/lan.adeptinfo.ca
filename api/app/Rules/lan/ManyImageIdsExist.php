<?php

namespace App\Rules\Lan;

use App\Model\Image;
use Illuminate\Contracts\Validation\Rule;

class ManyImageIdsExist implements Rule
{
    protected $badImageIds = [];

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value == null || !is_string($value)) {
            return true;
        }
        $imageIdArray = array_map('intval', explode(',', $value));
        for ($i = 0; $i < count($imageIdArray); $i++) {
            $image = Image::find($imageIdArray[$i]);
            if ($image == null) {
                array_push($this->badImageIds, $imageIdArray[$i]);
            }
        }
        return count($this->badImageIds) == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.many_image_ids_exist', [
            'ids' => implode(', ', $this->badImageIds),
            'attribute' => ':attribute'
        ]);
    }
}