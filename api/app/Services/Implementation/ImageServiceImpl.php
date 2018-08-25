<?php

namespace App\Services\Implementation;


use App\Model\Image;
use App\Repositories\Implementation\ImageRepositoryImpl;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Rules\ManyImageIdsExist;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImageServiceImpl implements ImageService
{
    protected $imageRepository;
    protected $lanRepository;

    /**
     * LanServiceImpl constructor.
     * @param ImageRepositoryImpl $imageRepositoryImpl
     * @param LanRepositoryImpl $lanRepositoryImpl
     */
    public function __construct(ImageRepositoryImpl $imageRepositoryImpl, LanRepositoryImpl $lanRepositoryImpl)
    {
        $this->imageRepository = $imageRepositoryImpl;
        $this->lanRepository = $lanRepositoryImpl;
    }

    public function addImage(Request $input): Image
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $rulesValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'image' => $input->input('image')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'image' => 'required|string'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        return $this->imageRepository->createImageForLan($input->input('lan_id'), $input->input('image'));
    }

    public function deleteImages(Request $input): array
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $rulesValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'images_id' => $input->input('images_id')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'images_id' => ['required', 'string', new ManyImageIdsExist]
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $badImageIds = [];
        $imageIdArray = array_map('intval', explode(',', $input->input('images_id')));
        for ($i = 0; $i < count($imageIdArray); $i++) {
            $image = Image::find($imageIdArray[$i]);
            if ($image == null) {
                array_push($badImageIds, $imageIdArray[$i]);
            }
        }

        $imageIdArray = array_map('intval', explode(',', $input->input('images_id')));
        for ($i = 0; $i < count($imageIdArray); $i++) {
            $image = $this->imageRepository->findImageById($imageIdArray[$i]);
            $this->imageRepository->deleteImage($image);
        }

        return $imageIdArray;
    }
}