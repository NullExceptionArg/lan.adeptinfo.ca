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

    public function addImage(Request $request)
    {
        return response()->json($this->imageService->addImage($request), 201);
    }

    public function deleteImages(Request $request)
    {
        // TODO Permissions delete-image
        return response()->json($this->imageService->deleteImages($request), 200);
    }
}