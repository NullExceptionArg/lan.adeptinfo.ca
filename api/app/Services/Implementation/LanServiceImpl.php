<?php

namespace App\Services\Implementation;

use App\Http\Resources\{Lan\GetAllResource, Lan\GetResource, Lan\ImageResource, Lan\UpdateResource};
use App\Model\Lan;
use App\Repositories\Implementation\{LanRepositoryImpl, RoleRepositoryImpl, SeatRepositoryImpl};
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
        // Créer l'image de LAN
        $imageId = $this->lanRepository->addLanImage($lanId, $image);

        // Retourner l'image créée
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
        // Vérifier s'il existe un LAN courant
        $hasNoCurrentLan = is_null(Lan::getCurrent());

        // Créer le LAN
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

        // Ajouter les rôles de LAN par défaut au LAN
        $this->roleRepository->addDefaultLanRoles($lanId);

        // Retourner le LAN créé
        return $this->lanRepository->findById($lanId);
    }

    public function deleteLanImages(string $imageIds): array
    {
        // Transformer la chaîne de caractère des images en tableau (Ex de chaîne : "1,6,2")
        $imageIdsArray = array_map('intval', explode(',', $imageIds));

        // Supprimer les images
        $this->lanRepository->deleteLanImages($imageIdsArray);

        // Retourner les ids des images supprimées
        return $imageIdsArray;
    }

    public function getAll(): ResourceCollection
    {
        return GetAllResource::collection($this->lanRepository->getAll());
    }

    public function get(int $lanId, ?string $fields, ?int $userId): GetResource
    {
        // Obtenir le nombre de places occupées
        $placeCount = $this->seatRepository->getReservedPlaces($lanId);

        // Obtenir les images du LAN
        $images = $this->lanRepository->getImagesForLan($lanId);

        // Trouver le LAN
        $lan = $this->lanRepository->findById($lanId);

        // Déterminer si l'utilisateur peut voir la clé secrète de seats.io
        $canSeeSeatsioSecretKey = null;
        if (is_null($userId)) {
            $canSeeSeatsioSecretKey = false;
        } else {
            $canSeeSeatsioSecretKey = $this->roleRepository->userHasPermission(
                'edit-lan',
                $userId,
                $lanId
            );
        }

        // Retourner les détails du LAN selon les champs spécifiés
        return new GetResource($lan, $placeCount, $images, $fields, $canSeeSeatsioSecretKey);
    }

    public function setCurrent(int $lanId): Lan
    {
        // Retirer l'état de courant au LAN courant
        $this->lanRepository->removeCurrent();

        // Rendre le LAN spécifié comme courant
        $this->lanRepository->setCurrent($lanId);

        // Retourner le LAN rendu comme courant
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
        // Mettre à jour les détails du LAN
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

        // Trouver le nombre de places réservées dans le LAN
        $placeCount = $this->seatRepository->getReservedPlaces($lanId);

        // Trouver les images du LAN
        $images = $this->lanRepository->getImagesForLan($lanId);

        // Trouver le LAN
        $lan = $this->lanRepository->findById($lanId);


        // Retourner les détails du LAN mis à jour
        return new UpdateResource($lan, $placeCount, $images);
    }
}
