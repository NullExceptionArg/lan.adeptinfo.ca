<?php

namespace App\Services\Implementation;


use App\Model\Image;
use App\Repositories\Implementation\ImageRepositoryImpl;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImageServiceImpl implements ImageService
{
    protected $imageRepository;

    /**
     * LanServiceImpl constructor.
     * @param ImageRepositoryImpl $imageRepositoryImpl
     */
    public function __construct(ImageRepositoryImpl $imageRepositoryImpl)
    {
        $this->imageRepository = $imageRepositoryImpl;
    }

    public function addImage(Request $request, string $lanId): Image
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
            'image' => $request->input('image')
        ], [
            'lan_id' => 'integer|exists:lan,id',
            'image' => 'required|string'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        return $this->imageRepository->createImageForLan($lanId, $request->input('image'));
    }

    public function deleteImages(string $lanId, string $imagesId): array
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
            'images_id' => $imagesId
        ], [
            'lan_id' => 'integer|exists:lan,id',
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $imageIdArray = array_map('intval', explode(',', $imagesId));
        $images = [];
        $badImageIds = [];
        for ($i = 0; $i < count($imageIdArray); $i++) {
            $image = $this->imageRepository->findImageById($imageIdArray[$i]);
            if ($image == null) {
                array_push($badImageIds, $imageIdArray[$i]);
            } else {
                array_push($images, $image);
            }
        }

        if (count($badImageIds) == 0) {
            foreach ($images as $image) {
                $this->imageRepository->deleteImage($image);
            }
        } else {
            throw new BadRequestHttpException(json_encode([
                "images_id" => [
                    'Images with id ' . implode(', ', $badImageIds) . ' don\'t exist.'
                ]
            ]));
        }

        return $imageIdArray;
    }
}