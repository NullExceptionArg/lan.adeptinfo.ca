<?php

namespace App\Repositories;


use App\Model\Image;
use App\Model\Lan;
use Illuminate\Support\Collection;

interface ImageRepository
{
    public function findImageById(int $imageId): ?Image;

    public function createImageForLan(int $lanId, string $imageContent): Image;

    public function deleteImage(Image $image): int;

    public function getImagesForLan(Lan $lan): Collection;
}