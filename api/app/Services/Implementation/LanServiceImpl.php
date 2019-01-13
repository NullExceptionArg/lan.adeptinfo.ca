<?php

namespace App\Services\Implementation;

use App\Http\Resources\Lan\GetAllResource;
use App\Http\Resources\Lan\GetResource;
use App\Http\Resources\Lan\ImageResource;
use App\Http\Resources\Lan\UpdateResource;
use App\Model\Lan;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\RoleRepositoryImpl;
use App\Repositories\Implementation\SeatRepositoryImpl;
use App\Services\LanService;
use DateTime;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LanServiceImpl implements LanService
{
    protected $lanRepository;
    protected $roleRepository;
    protected $seatRepository;

    /**
     * LanServiceImpl constructor.
     * @param LanRepositoryImpl $lanRepository
     * @param RoleRepositoryImpl $roleRepository
     * @param SeatRepositoryImpl $seatRepository
     */
    public function __construct(
        LanRepositoryImpl $lanRepository,
        RoleRepositoryImpl $roleRepository,
        SeatRepositoryImpl $seatRepository
    )
    {
        $this->lanRepository = $lanRepository;
        $this->roleRepository = $roleRepository;
        $this->seatRepository = $seatRepository;
    }

    public function addLanImage(int $lanId, string $image): ImageResource
    {
        $imageId = $this->lanRepository->addLanImage($lanId, $image);
        return new ImageResource($this->lanRepository->findLanImageById($imageId));
    }

    public function create(
        string $name,
        DateTime $lanStart,
        DateTime $lanEnd,
        DateTime $seatReservationStart,
        DateTime $tournamentReservationStart,
        string $eventKey,
        string $publicKey,
        string $secretKey,
        float $latitude,
        float $longitude,
        int $places,
        ?int $price,
        ?string $rules,
        ?string $description
    ): Lan
    {
        $hasNoCurrentLan = Lan::getCurrent() == null;

        $lanId = $this->lanRepository->create
        (
            $name,
            $lanStart,
            $lanEnd,
            $seatReservationStart,
            $tournamentReservationStart,
            $eventKey,
            $publicKey,
            $secretKey,
            $latitude,
            $longitude,
            $places,
            $hasNoCurrentLan,
            $price,
            $rules,
            $description
        );

        $this->roleRepository->addDefaultLanRoles($lanId);
        return $this->lanRepository->findById($lanId);
    }

    public function deleteLanImages(string $imageIds): array
    {
        $imageIdsArray = array_map('intval', explode(',', $imageIds));
        $this->lanRepository->deleteLanImages($imageIdsArray);

        return $imageIdsArray;
    }

    public function getAll(): ResourceCollection
    {
        return GetAllResource::collection($this->lanRepository->getAll());
    }

    public function get(int $lanId, ?string $fields): GetResource
    {
        $placeCount = $this->seatRepository->getReservedPlaces($lanId);
        $images = $this->lanRepository->getImagesForLan($lanId);
        $lan = $this->lanRepository->findById($lanId);

        return new GetResource($lan, $placeCount, $images, $fields);
    }

    public function setCurrent(int $lanId): Lan
    {
        $this->lanRepository->removeCurrent();
        $this->lanRepository->setCurrent($lanId);
        return $this->lanRepository->findById($lanId);
    }

    public function update(
        int $lanId,
        ?string $name,
        ?DateTime $lanStart,
        ?DateTime $lanEnd,
        ?DateTime $seatReservationStart,
        ?DateTime $tournamentReservationStart,
        ?string $eventKey,
        ?string $publicKey,
        ?string $secretKey,
        ?float $latitude,
        ?float $longitude,
        ?int $places,
        ?int $price,
        ?string $rules,
        ?string $description
    ): UpdateResource
    {
        $this->lanRepository->update(
            $lanId,
            $name,
            $lanStart,
            $lanEnd,
            $seatReservationStart,
            $tournamentReservationStart,
            $eventKey,
            $publicKey,
            $secretKey,
            $latitude,
            $longitude,
            $places,
            $price,
            $rules,
            $description
        );

        $placeCount = $this->seatRepository->getReservedPlaces($lanId);
        $images = $this->lanRepository->getImagesForLan($lanId);
        $lan = $this->lanRepository->findById($lanId);

        return new UpdateResource($lan, $placeCount, $images);
    }
}
