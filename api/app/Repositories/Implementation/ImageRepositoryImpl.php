<?php

namespace App\Repositories\Implementation;


use App\Model\Image;
use App\Model\Lan;
use App\Repositories\ImageRepository;
use Illuminate\Support\Collection;

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

    public function getImagesForLan(Lan $lan): Collection
    {
        return $lan->Image()->get();
    }
}