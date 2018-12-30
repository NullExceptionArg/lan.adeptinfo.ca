<?php

namespace App\Http\Controllers;

use App\Rules\HasPermission;
use App\Rules\HasPermissionInLan;
use App\Rules\LowerReservedPlace;
use App\Rules\ManyImageIdsExist;
use App\Rules\ValidEventKey;
use App\Rules\ValidSecretKey;
use App\Services\Implementation\LanServiceImpl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LanController extends Controller
{
    protected $lanService;

    /**
     * LanController constructor.
     * @param LanServiceImpl $lanServiceImpl
     */
    public function __construct(LanServiceImpl $lanServiceImpl)
    {
        $this->lanService = $lanServiceImpl;
    }

    public function addImage(Request $request)
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

        return response()->json($this->lanService->addImage(
            $request->input('lan_id'),
            $request->input('image')
        ), 201);
    }

    public function create(Request $request)
    {
        $validator = Validator::make([
            'name' => $request->input('name'),
            'lan_start' => $request->input('lan_start'),
            'lan_end' => $request->input('lan_end'),
            'seat_reservation_start' => $request->input('seat_reservation_start'),
            'tournament_reservation_start' => $request->input('tournament_reservation_start'),
            'event_key' => $request->input('event_key'),
            'public_key' => $request->input('public_key'),
            'secret_key' => $request->input('secret_key'),
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
            'event_key' => ['required', 'string', 'max:255', new ValidEventKey(null, $request->input('secret_key'))],
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

        $this->checkValidation($validator);

        return response()->json($this->lanService->create(
            $request->input('name'),
            Carbon::parse($request->input('lan_start')),
            Carbon::parse($request->input('lan_end')),
            Carbon::parse($request->input('seat_reservation_start')),
            Carbon::parse($request->input('tournament_reservation_start')),
            $request->input('event_key'),
            $request->input('public_key'),
            $request->input('secret_key'),
            $request->input('latitude'),
            $request->input('longitude'),
            $request->input('places'),
            intval($request->input('price')),
            $request->input('rules'),
            $request->input('description')
        ), 201);
    }

    public function deleteImages(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'image_ids' => $request->input('image_ids'),
            'permission' => 'delete-image'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'image_ids' => ['required', 'string', new ManyImageIdsExist],
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->lanService->deleteImages(
            $request->input('image_ids')
        ), 200);
    }

    public function getAll()
    {
        return response()->json($this->lanService->getAll(), 200);
    }

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
            $request->input('fields')
        ), 200);
    }

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
            'public_key' => $request->input('public_key'),
            'secret_key' => $request->input('secret_key'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'places' => $request->input('places'),
            'price' => $request->input('price'),
            'rules' => $request->input('rules'),
            'description' => $request->input('description'),
            'permission' => 'edit-lan',
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'name' => 'string|max:255',
            'lan_start' => 'after:seat_reservation_start|after:tournament_reservation_start',
            'lan_end' => 'after:lan_start',
            'seat_reservation_start' => 'before_or_equal:lan_start',
            'tournament_reservation_start' => 'before_or_equal:lan_start',
            'event_key' => ['string', 'max:255', new ValidEventKey($request->input('lan_id'), null)],
            'public_key' => 'string|max:255',
            'secret_key' => ['string', 'max:255', new ValidSecretKey],
            'latitude' => 'numeric|min:-85|max:85',
            'longitude' => 'numeric|min:-180|max:180',
            'places' => ['integer', 'min:1', new LowerReservedPlace($request->input('lan_id'))],
            'price' => 'integer|min:0',
            'rules' => 'string',
            'description' => 'string',
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
            $request->input('public_key'),
            $request->input('secret_key'),
            $request->input('latitude'),
            $request->input('longitude'),
            $request->input('places'),
            intval($request->input('price')),
            $request->input('rules'),
            $request->input('description')
        ), 200);
    }
}
