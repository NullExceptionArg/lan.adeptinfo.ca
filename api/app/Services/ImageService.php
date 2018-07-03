<?php

namespace App\Services;


use App\Model\Image;
use Illuminate\Http\Request;

interface ImageService
{
    public function addImage(Request $request, string $lanId): Image;

    public function deleteImages(string $lanId, string $imagesId): array;
}