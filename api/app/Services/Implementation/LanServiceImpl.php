<?php


namespace App\Services\Implementation;

use App\Http\Resources\Lan\GetLanResource;
use App\Http\Resources\Lan\GetAllLanResource;
use App\Http\Resources\Lan\UpdateLanResource;
use App\Model\Lan;
use App\Repositories\Implementation\ImageRepositoryImpl;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Rules\LowerReservedPlace;
use App\Rules\ValidEventKey;
use App\Rules\ValidSecretKey;
use App\Services\LanService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LanServiceImpl implements LanService
{
    protected $lanRepository;
    protected $imageRepository;

    /**
     * LanServiceImpl constructor.
     * @param LanRepositoryImpl $lanRepositoryImpl
     * @param ImageRepositoryImpl $imageRepositoryImpl
     */
    public function __construct(LanRepositoryImpl $lanRepositoryImpl, ImageRepositoryImpl $imageRepositoryImpl)
    {
        $this->lanRepository = $lanRepositoryImpl;
        $this->imageRepository = $imageRepositoryImpl;
    }

    public function createLan(Request $input): Lan
    {
        $lanValidator = Validator::make($input->all(), [
            'name' => 'required|string|max:255',
            'lan_start' => 'required|after:seat_reservation_start|after:tournament_reservation_start',
            'lan_end' => 'required|after:lan_start',
            'seat_reservation_start' => 'required|before_or_equal:lan_start',
            'tournament_reservation_start' => 'required|before_or_equal:lan_start',
            'event_key' => ['required', 'string', 'max:255', new ValidEventKey(null, $input->input('secret_key'))],
            'public_key' => 'required|string|max:255',
            'secret_key' => ['required', 'string', 'max:255', new ValidSecretKey],
            'latitude' => 'required|numeric|min:-85|max:85',
            'longitude' => 'required|numeric|min:-180|max:180',
            'places' => 'required|integer|min:1',
            'price' => 'integer|min:0',
            'rules' => 'string',
            'description' => 'string'
        ]);

        if ($lanValidator->fails()) {
            throw new BadRequestHttpException($lanValidator->errors());
        }

        $hasNoCurrentLan = $this->lanRepository->getCurrentLan() == null;

        return $this->lanRepository->createLan
        (
            $input->input('name'),
            new DateTime($input->input('lan_start')),
            new DateTime($input->input('lan_end')),
            new DateTime($input->input('seat_reservation_start')),
            new DateTime($input->input('tournament_reservation_start')),
            $input->input('event_key'),
            $input->input('public_key'),
            $input->input('secret_key'),
            $input->input('latitude'),
            $input->input('longitude'),
            $input->input('places'),
            $hasNoCurrentLan,
            intval($input->input('price')),
            $input->input('rules'),
            $input->input('description')
        );
    }

    public function getLan(Request $input): GetLanResource
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrentLan();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $rulesValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
        ], [
            'lan_id' => 'integer|exists:lan,id'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findLanById($input->input('lan_id'));
        }
        $placeCount = $this->lanRepository->getReservedPlaces($input->input('lan_id'));
        $images = $this->imageRepository->getImagesForLan($lan);

        return new GetLanResource($lan, $placeCount, $images);
    }

    public function getAllLan(): ResourceCollection
    {
        return GetAllLanResource::collection($this->lanRepository->getAllLan());
    }

    public function setCurrentLan(Request $input): int
    {
        $rulesValidator = Validator::make([
            'lan_id' => $input->input('lan_id')
        ], [
            'lan_id' => 'required|integer|exists:lan,id'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $this->lanRepository->removeCurrentLan();

        $this->lanRepository->setCurrentLan($input->input('lan_id'));

        return $input->input('lan_id');
    }

    public function update(Request $input): UpdateLanResource
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrentLan();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $lanValidator = Validator::make($input->all(), [
            'name' => 'string|max:255',
            'lan_start' => 'after:seat_reservation_start|after:tournament_reservation_start',
            'lan_end' => 'after:lan_start',
            'seat_reservation_start' => 'before_or_equal:lan_start',
            'tournament_reservation_start' => 'before_or_equal:lan_start',
            'event_key' => ['string', 'max:255', new ValidEventKey($input->input('lan_id'), null)],
            'public_key' => 'string|max:255',
            'secret_key' => ['string', 'max:255', new ValidSecretKey],
            'latitude' => 'numeric|min:-85|max:85',
            'longitude' => 'numeric|min:-180|max:180',
            'places' => ['integer', 'min:1', new LowerReservedPlace($input->input('lan_id'))],
            'price' => 'integer|min:0',
            'rules' => 'string',
            'description' => 'string'
        ]);

        if ($lanValidator->fails()) {
            throw new BadRequestHttpException($lanValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findLanById($input->input('lan_id'));
        }

        $placeCount = $this->lanRepository->getReservedPlaces($lan->id);
        $images = $this->imageRepository->getImagesForLan($lan);

        return new UpdateLanResource($this->lanRepository->updateLan(
            $lan,
            $input->input('name'),
            new DateTime($input->input('lan_start')),
            new DateTime($input->input('lan_end')),
            new DateTime($input->input('seat_reservation_start')),
            new DateTime($input->input('tournament_reservation_start')),
            $input->input('event_key'),
            $input->input('public_key'),
            $input->input('secret_key'),
            $input->input('latitude'),
            $input->input('longitude'),
            $input->input('places'),
            intval($input->input('price')),
            $input->input('rules'),
            $input->input('description')
        ), $placeCount, $images);
    }
}