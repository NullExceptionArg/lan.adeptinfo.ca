<?php

namespace App\Http\Controllers;


use App\Services\Implementation\ImageServiceImpl;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    protected $imageService;

    /*
    * LanController constructor.
    * @param LanServiceImpl $lanServiceImpl
    */
    public function __construct(ImageServiceImpl $imageServiceImpl)
    {
        $this->imageService = $imageServiceImpl;
    }

    public function addImage(Request $request, string $lan_id)
    {
        return response()->json($this->imageService->addImage($request, $lan_id), 201);
    }

    public function deleteImages(string $lan_id, string $images_id)
    {
        $this->imageService->deleteImages($lan_id, $images_id);
        return response()->json([], 200);
    }
}