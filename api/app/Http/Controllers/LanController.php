<?php

namespace App\Http\Controllers;

use App\Rules\{Image\HasPermissionInLan as HasPermissionInLanImages,
    Lan\LowerReservedPlace,
    Lan\ManyImageIdsExist,
    Seat\ValidEventKey,
    User\HasPermission,
    User\HasPermissionInLan};
use App\Services\Implementation\LanServiceImpl;
use Carbon\Carbon;
use Illuminate\{Http\JsonResponse, Http\Request, Support\Facades\Auth, Support\Facades\Validator};

/**
 * Validation et application de la logique applicative sur les LANs.
 *
 * Class LanController
 * @package App\Http\Controllers
 */
class LanController extends Controller
{
    /**
     * Service de LAN.
     *
     * @var LanServiceImpl
     */
    protected $lanService;

    /**
     * LanController constructor.
     * @param LanServiceImpl $lanServiceImpl
     */
    public function __construct(LanServiceImpl $lanServiceImpl)
    {
        $this->lanService = $lanServiceImpl;
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#ajouter-une-image
     * @param Request $request
     * @return JsonResponse
     */
    public function addLanImage(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'image' => $request->input('image'),
            'permission' => 'add-image'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'image' => 'required|string',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->lanService->addLanImage(
            $request->input('lan_id'),
            $request->input('image')
        ), 201);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#creer-un-lan
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $validator = Validator::make([
            'name' => $request->input('name'),
            'lan_start' => $request->input('lan_start'),
            'lan_end' => $request->input('lan_end'),
            'seat_reservation_start' => $request->input('seat_reservation_start'),
            'tournament_reservation_start' => $request->input('tournament_reservation_start'),
            'event_key' => $request->input('event_key'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'places' => $request->input('places'),
            'price' => $request->input('price'),
            'rules' => $request->input('rules'),
            'description' => $request->input('description'),
            'permission' => 'create-lan',
        ], [
            'name' => 'required|string|max:255',
            'lan_start' => 'required|after:seat_reservation_start|after:tournament_reservation_start',
            'lan_end' => 'required|after:lan_start',
            'seat_reservation_start' => 'required|before_or_equal:lan_start',
            'tournament_reservation_start' => 'required|before_or_equal:lan_start',
            'event_key' => ['required', 'string', 'max:255', new ValidEventKey],
            'latitude' => 'required|numeric|min:-85|max:85',
            'longitude' => 'required|numeric|min:-180|max:180',
            'places' => 'required|integer|min:1',
            'price' => 'integer|min:0',
            'rules' => 'nullable|string',
            'description' => 'nullable|string',
            'permission' => new HasPermission(Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->lanService->create(
            $request->input('name'),
            Carbon::parse($request->input('lan_start')),
            Carbon::parse($request->input('lan_end')),
            Carbon::parse($request->input('seat_reservation_start')),
            Carbon::parse($request->input('tournament_reservation_start')),
            $request->input('event_key'),
            $request->input('latitude'),
            $request->input('longitude'),
            $request->input('places'),
            intval($request->input('price')),
            $request->input('rules'),
            $request->input('description')
        ), 201);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#supprimer-des-images
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteLanImages(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'image_ids' => $request->input('image_ids'),
            'permission' => 'delete-image'
        ], [
            'image_ids' => ['required', 'string', new ManyImageIdsExist],
            'permission' => new HasPermissionInLanImages($request->input('image_ids'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->lanService->deleteLanImages(
            $request->input('image_ids')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#lister-les-lans
     * @return JsonResponse
     */
    public function getAll()
    {
        return response()->json($this->lanService->getAll(), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#details-d-39-un-lan
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL'
        ]);

        $this->checkValidation($validator);

        return response()->json($this->lanService->get(
            $request->input('lan_id'),
            $request->input('fields'),
            Auth::id()
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#changer-de-lan-courant
     * @param Request $request
     * @return JsonResponse
     */
    public function setCurrent(Request $request)
    {
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'permission' => 'set-current-lan'
        ], [
            'lan_id' => 'required|integer|exists:lan,id,deleted_at,NULL',
            'permission' => new HasPermission(Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->lanService->setCurrent(
            $request->input('lan_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#mettre-a-jour-un-lan
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'name' => $request->input('name'),
            'lan_start' => $request->input('lan_start'),
            'lan_end' => $request->input('lan_end'),
            'seat_reservation_start' => $request->input('seat_reservation_start'),
            'tournament_reservation_start' => $request->input('tournament_reservation_start'),
            'event_key' => $request->input('event_key'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'places' => $request->input('places'),
            'price' => $request->input('price'),
            'rules' => $request->input('rules'),
            'description' => $request->input('description'),
            'permission' => 'edit-lan',
        ], [
            'lan_id' => 'nullable|integer|exists:lan,id,deleted_at,NULL',
            'name' => 'nullable|string|max:255',
            'lan_start' => 'nullable|after:seat_reservation_start|after:tournament_reservation_start',
            'lan_end' => 'nullable|after:lan_start',
            'seat_reservation_start' => 'nullable|before_or_equal:lan_start',
            'tournament_reservation_start' => 'nullable|before_or_equal:lan_start',
            'event_key' => [
                'nullable',
                'string',
                'max:255',
                new ValidEventKey
            ],
            'latitude' => 'nullable|numeric|min:-85|max:85',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'places' => ['nullable', 'integer', 'min:1', new LowerReservedPlace($request->input('lan_id'))],
            'price' => 'nullable|integer|min:0',
            'rules' => 'nullable|string',
            'description' => 'nullable|string',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->lanService->update(
            $request->input('lan_id'),
            $request->input('name'),
            Carbon::parse($request->input('lan_start')),
            Carbon::parse($request->input('lan_end')),
            Carbon::parse($request->input('seat_reservation_start')),
            Carbon::parse($request->input('tournament_reservation_start')),
            $request->input('event_key'),
            $request->input('latitude'),
            $request->input('longitude'),
            $request->input('places'),
            intval($request->input('price')),
            $request->input('rules'),
            $request->input('description')
        ), 200);
    }
}
