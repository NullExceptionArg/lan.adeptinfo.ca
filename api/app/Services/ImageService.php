<?php

namespace App\Services;


use App\Model\Image;
use Illuminate\Http\Request;

interface ImageService
{
    public function addImage(Request $input): Image;

    public function deleteImages(Request $input): array;
}