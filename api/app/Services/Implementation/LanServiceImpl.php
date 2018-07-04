<?php


namespace App\Services\Implementation;

use App\Http\Resources\Lan\GetLanResource;
use App\Http\Resources\Lan\GetLansResource;
use App\Model\Lan;
use App\Repositories\Implementation\ImageRepositoryImpl;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Services\LanService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Validator;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioException;
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

        // Internal validation

        $lanValidator = Validator::make($input->all(), [
            'name' => 'required|string|max:255',
            'lan_start' => 'required|after:seat_reservation_start|after:tournament_reservation_start',
            'lan_end' => 'required|after:lan_start',
            'seat_reservation_start' => 'required|after_or_equal:now',
            'tournament_reservation_start' => 'required|after_or_equal:now',
            'event_key_id' => 'required|string|max:255',
            'public_key_id' => 'required|string|max:255',
            'secret_key_id' => 'required|string|max:255',
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

        // Seats.io validation

        $seatsClient = new SeatsioClient($input['secret_key_id']);
        // Test if secret key is id valid
        try {
            $seatsClient->charts()->listAllTags();
        } catch (SeatsioException $exception) {
            throw new BadRequestHttpException(json_encode([
                "secret_key_id" => [
                    'Secret key id: ' . $input['secret_key_id'] . ' is not valid.'
                ]
            ]));
        }

        // Test if event key id is valid
        try {
            $seatsClient->events()->retrieve($input['event_key_id']);
        } catch (SeatsioException $exception) {
            throw new BadRequestHttpException(json_encode([
                "event_key_id" => [
                    'Event key id: ' . $input['event_key_id'] . ' is not valid.'
                ]
            ]));
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

    public function updateRules(Request $input, string $lanId): array
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
            'rules' => $input->input('rules')
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'rules' => 'required|string',
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        $this->lanRepository->updateLanRules($lan, $input->input('rules'));

        return ["rules" => $input->input('rules')];
    }

    public function updateLanName(Request $input, string $lanId): array
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
            'name' => $input->input('name')
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'name' => 'required|string|max:255',
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        $this->lanRepository->updateLanName($lan, $input->input('name'));

        return ["name" => $input->input('name')];
    }

    public function updateLanPrice(Request $input, string $lanId): array
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
            'price' => $input->input('price')
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'price' => 'required|integer|min:0',
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        $this->lanRepository->updateLanPrice($lan, $input->input('price'));

        return ["price" => $input->input('price')];
    }

    public function updateLanLocation(Request $input, string $lanId): array
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
            'longitude' => $input->input('longitude'),
            'latitude' => $input->input('latitude')
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'longitude' => 'required|numeric|min:-180|max:180',
            'latitude' => 'required|numeric|min:-85|max:85'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        $this->lanRepository->updateLanLocation(
            $lan,
            $input->input('longitude'),
            $input->input('latitude')
        );

        return [
            "longitude" => $input->input('longitude'),
            "latitude" => $input->input('latitude')
        ];
    }

    public function updateLanSeatReservationStart(Request $input, string $lanId): array
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
            'seat_reservation_start' => $input->input('seat_reservation_start')
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'seat_reservation_start' => 'required|after_or_equal:now'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        $this->lanRepository->updateLanSeatReservationStart($lan, new DateTime($input->input('seat_reservation_start')));

        return ["seat_reservation_start" => $input->input('seat_reservation_start')];
    }

    public function updateLanTournamentReservationStart(Request $input, string $lanId): array
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
            'tournament_reservation_start' => $input->input('tournament_reservation_start')
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'tournament_reservation_start' => 'required|after_or_equal:now'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        $this->lanRepository->updateLanTournamentReservationStart($lan, new DateTime($input->input('tournament_reservation_start')));

        return ["tournament_reservation_start" => $input->input('tournament_reservation_start')];
    }

    public function updateLanStartDate(Request $input, string $lanId): array
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
            'lan_start' => $input->input('lan_start')
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'lan_start' => 'required|after:seat_reservation_start|after:tournament_reservation_start'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        $this->lanRepository->updateLanStartDate($lan, new DateTime($input->input('lan_start')));

        return ["lan_start" => $input->input('lan_start')];
    }

    public function updateLanEndDate(Request $input, string $lanId): array
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
            'lan_end' => $input->input('lan_end')
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'lan_end' => 'required|after:lan_start'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        $this->lanRepository->updateLanEndDate($lan, new DateTime($input->input('lan_end')));

        return ["lan_end" => $input->input('lan_end')];
    }

    public function updateLanSeatsKeys(Request $input, string $lanId): array
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
            'event_key_id' => $input->input('event_key_id'),
            'public_key_id' => $input->input('public_key_id'),
            'secret_key_id' => $input->input('secret_key_id')
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'event_key_id' => 'required|string|max:255',
            'public_key_id' => 'required|string|max:255',
            'secret_key_id' => 'required|string|max:255'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);


        $this->lanRepository->updateLanSeatsKeys(
            $lan,
            $input->input('event_key_id'),
            $input->input('lan_end'),
            $input->input('lan_end')
        );

        return [
            "event_key_id" => $input->input('event_key_id'),
            "public_key_id" => $input->input('public_key_id'),
            "secret_key_id" => $input->input('secret_key_id')
        ];
    }

    public function updateLanDescription(Request $input, string $lanId): array
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
            'description' => $input->input('description')
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'description' => 'required|string'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        $this->lanRepository->updateLanDescription($lan, $input->input('description'));

        return ["description" => $input->input('description')];
    }

    public function updateLanPlaces(Request $input, string $lanId): array
    {
        $rulesValidator = Validator::make([
            'lan_id' => $lanId,
            'places' => $input->input('places')
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'places' => 'required|integer|min:1'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        $this->lanRepository->updateLanPlaces($lan, $input->input('places'));

        return ["places" => $input->input('places')];
    }
}