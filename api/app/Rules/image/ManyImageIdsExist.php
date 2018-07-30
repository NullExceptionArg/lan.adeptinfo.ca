<?php

namespace App\Rules;

use App\Model\Image;
use Illuminate\Contracts\Validation\Rule;

class ManyImageIdsExist implements Rule
{

    protected $badImageIds = [];

    public function __construct(string $imageIds)
    {
        $imageIdArray = array_map('intval', explode(',', $imageIds));
        for ($i = 0; $i < count($imageIdArray); $i++) {
            $image = Image::find($imageIdArray[$i]);
            if ($image == null) {
                array_push($this->badImageIds, $imageIdArray[$i]);
            }
        }
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