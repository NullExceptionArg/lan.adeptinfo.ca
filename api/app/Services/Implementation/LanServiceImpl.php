<?php


namespace App\Services\Implementation;

use App\Http\Resources\Lan\GetAllResource;
use App\Http\Resources\Lan\GetResource;
use App\Http\Resources\Lan\UpdateResource;
use App\Model\Lan;
use App\Repositories\Implementation\ImageRepositoryImpl;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\RoleRepositoryImpl;
use App\Rules\HasPermission;
use App\Rules\HasPermissionInLan;
use App\Rules\LowerReservedPlace;
use App\Rules\ValidEventKey;
use App\Rules\ValidSecretKey;
use App\Services\LanService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LanServiceImpl implements LanService
{
    protected $lanRepository;
    protected $roleRepository;
    protected $imageRepository;

    /**
     * LanServiceImpl constructor.
     * @param LanRepositoryImpl $lanRepositoryImpl
     * @param ImageRepositoryImpl $imageRepositoryImpl
     * @param RoleRepositoryImpl $roleRepository
     */
    public function __construct(
        LanRepositoryImpl $lanRepositoryImpl,
        ImageRepositoryImpl $imageRepositoryImpl,
        RoleRepositoryImpl $roleRepository
    )
    {
        $this->lanRepository = $lanRepositoryImpl;
        $this->imageRepository = $imageRepositoryImpl;
        $this->roleRepository = $roleRepository;
    }

    public function create(Request $input): Lan
    {
        $lanValidator = Validator::make([
            'name' => $input->input('name'),
            'lan_start' => $input->input('lan_start'),
            'lan_end' => $input->input('lan_end'),
            'seat_reservation_start' => $input->input('seat_reservation_start'),
            'tournament_reservation_start' => $input->input('tournament_reservation_start'),
            'event_key' => $input->input('event_key'),
            'public_key' => $input->input('public_key'),
            'secret_key' => $input->input('secret_key'),
            'latitude' => $input->input('latitude'),
            'longitude' => $input->input('longitude'),
            'places' => $input->input('places'),
            'price' => $input->input('price'),
            'rules' => $input->input('rules'),
            'description' => $input->input('description'),
            'permission' => 'create-lan',
        ], [
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
            'description' => 'string',
            'permission' => new HasPermission(Auth::id())
        ]);

        if ($lanValidator->fails()) {
            throw new BadRequestHttpException($lanValidator->errors());
        }

        $hasNoCurrentLan = $this->lanRepository->getCurrent() == null;

        $lan = $this->lanRepository->create
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

        $this->roleRepository->createDefaultLanRoles($lan->id);

        return $lan;
    }

    public function get(Request $input): GetResource
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $rulesValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL'
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }
        $placeCount = $this->lanRepository->getReservedPlaces($input->input('lan_id'));
        $images = $this->imageRepository->getImagesForLan($lan);

        return new GetResource($lan, $placeCount, $images);
    }

    public function getAll(): ResourceCollection
    {
        return GetAllResource::collection($this->lanRepository->getAll());
    }

    public function setCurrent(Request $input): int
    {
        $rulesValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'permission' => 'set-current-lan'
        ], [
            'lan_id' => 'required|integer|exists:lan,id,deleted_at,NULL',
            'permission' => new HasPermission(Auth::id())
        ]);

        if ($rulesValidator->fails()) {
            throw new BadRequestHttpException($rulesValidator->errors());
        }

        $this->lanRepository->removeCurrent();

        $this->lanRepository->setCurrent($input->input('lan_id'));

        return $input->input('lan_id');
    }

    public function edit(Request $input): UpdateResource
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $lanValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'name' => $input->input('name'),
            'lan_start' => $input->input('lan_start'),
            'lan_end' => $input->input('lan_end'),
            'seat_reservation_start' => $input->input('seat_reservation_start'),
            'tournament_reservation_start' => $input->input('tournament_reservation_start'),
            'event_key' => $input->input('event_key'),
            'public_key' => $input->input('public_key'),
            'secret_key' => $input->input('secret_key'),
            'latitude' => $input->input('latitude'),
            'longitude' => $input->input('longitude'),
            'places' => $input->input('places'),
            'price' => $input->input('price'),
            'rules' => $input->input('rules'),
            'description' => $input->input('description'),
            'permission' => 'edit-lan',
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
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
            'description' => 'string',
            'permission' => new HasPermissionInLan($input->input('lan_id'), Auth::id())
        ]);

        if ($lanValidator->fails()) {
            throw new BadRequestHttpException($lanValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        $placeCount = $this->lanRepository->getReservedPlaces($lan->id);
        $images = $this->imageRepository->getImagesForLan($lan);

        return new UpdateResource($this->lanRepository->update(
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