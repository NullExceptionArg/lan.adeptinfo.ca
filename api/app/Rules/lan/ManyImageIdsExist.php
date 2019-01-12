<?php

namespace App\Rules\Lan;

use App\Model\LanImage;
use Illuminate\Contracts\Validation\Rule;

class ManyImageIdsExist implements Rule
{
    protected $badImageIds = [];

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if ($value == null || !is_string($value)) {
            return true;
        }
        $imageIdArray = array_map('intval', explode(',', $value));
        for ($i = 0; $i < count($imageIdArray); $i++) {
            $image = LanImage::find($imageIdArray[$i]);
            if ($image == null) {
                array_push($this->badImageIds, $imageIdArray[$i]);
            }
        }
        return count($this->badImageIds) == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.many_image_ids_exist', [
            'ids' => implode(', ', $this->badImageIds),
            'attribute' => ':attribute'
        ]);
    }
}