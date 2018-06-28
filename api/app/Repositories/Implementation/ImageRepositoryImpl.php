<?php

namespace App\Repositories\Implementation;


use App\Model\Image;
use App\Repositories\ImageRepository;

class ImageRepositoryImpl implements ImageRepository
{

    public function findImageById(int $imageId): ?Image
    {
        return Image::find($imageId);
    }

    public function createImageForLan(int $lanId, string $imageContent): Image
    {
        $image = new Image();
        $image->lan_id = $lanId;
        $image->image = $imageContent;

        $image->save();

        return $image;
    }

    public function deleteImage(Image $image): int
    {
        try {
            $image->delete();
        } catch (\Exception $e) {
        }
        return $image->id;
    }
}