<?php


namespace App\Services\Implementation;

use App\Http\Resources\Lan\GetLanResource;
use App\Http\Resources\Lan\GetLansResource;
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
            'event_key_id' => ['required', 'string', 'max:255', new ValidEventKey(null, $input->input('secret_key_id'))],
            'public_key_id' => 'required|string|max:255',
            'secret_key_id' => ['required', 'string', 'max:255', new ValidSecretKey],
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

        return $this->lanRepository->createLan
        (
            $input->input('name'),
            new DateTime($input->input('lan_start')),
            new DateTime($input->input('lan_end')),
            new DateTime($input->input('seat_reservation_start')),
            new DateTime($input->input('tournament_reservation_start')),
            $input->input('event_key_id'),
            $input->input('public_key_id'),
            $input->input('secret_key_id'),
            $input->input('latitude'),
            $input->input('longitude'),
            $input->input('places'),
            intval($input->input('price')),
            $input->input('rules'),
            $input->input('description')
        );
    }

    public function getLan(Request $request, string $lanId): GetLanResource
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
        ], [
            'lan_id' => 'required|integer|exists:lan,id'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);
        $placeCount = $this->lanRepository->getReservedPlaces($lanId);
        $images = $this->imageRepository->getImagesForLan($lan);

        return new GetLanResource($lan, $placeCount, $images);
    }

    public function getLans(): ResourceCollection
    {
        return GetLansResource::collection($this->lanRepository->getLans());
    }

    public function setCurrentLan(string $lanId): int
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId
        ], [
            'lan_id' => 'integer|exists:lan,id'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $this->lanRepository->removeCurrentLan();

        $this->lanRepository->setCurrentLan($lanId);

        return $lanId;
    }

    public function getCurrentLan(): ?GetLanResource
    {
        $lan = $this->lanRepository->getCurrentLan();
        if ($lan != null) {
            $placeCount = $this->lanRepository->getReservedPlaces($lan->id);
            $images = $this->imageRepository->getImagesForLan($lan);
            return new GetLanResource($lan, $placeCount, $images);
        } else {
            return null;
        }
    }

    public function update(Request $input, string $lanId): UpdateLanResource
    {
        $lanValidator = Validator::make($input->all(), [
            'name' => 'string|max:255',
            'lan_start' => 'after:seat_reservation_start|after:tournament_reservation_start',
            'lan_end' => 'after:lan_start',
            'seat_reservation_start' => 'before_or_equal:lan_start',
            'tournament_reservation_start' => 'before_or_equal:lan_start',
            'event_key_id' => ['string', 'max:255', new ValidEventKey($lanId, null)],
            'public_key_id' => 'string|max:255',
            'secret_key_id' => ['string', 'max:255', new ValidSecretKey],
            'latitude' => 'numeric|min:-85|max:85',
            'longitude' => 'numeric|min:-180|max:180',
            'places' => ['integer', 'min:1', new LowerReservedPlace($lanId)],
            'price' => 'integer|min:0',
            'rules' => 'string',
            'description' => 'string'
        ]);

        if ($lanValidator->fails()) {
            throw new BadRequestHttpException($lanValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);
        $placeCount = $this->lanRepository->getReservedPlaces($lan->id);
        $images = $this->imageRepository->getImagesForLan($lan);

        return new UpdateLanResource($this->lanRepository->updateLan(
            $lan,
            $input->input('name'),
            new DateTime($input->input('lan_start')),
            new DateTime($input->input('lan_end')),
            new DateTime($input->input('seat_reservation_start')),
            new DateTime($input->input('tournament_reservation_start')),
            $input->input('event_key_id'),
            $input->input('public_key_id'),
            $input->input('secret_key_id'),
            $input->input('latitude'),
            $input->input('longitude'),
            $input->input('places'),
            intval($input->input('price')),
            $input->input('rules'),
            $input->input('description')
        ), $placeCount, $images);
    }
}