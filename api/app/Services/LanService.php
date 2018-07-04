<?php


namespace App\Services;

use App\Http\Resources\Lan\GetLanResource;
use App\Model\Lan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

interface LanService
{
    public function createLan(Request $input): Lan;

    public function getLan(Request $request, string $lanId): GetLanResource;

    public function getLans(): ResourceCollection;

    public function setCurrentLan(string $lanId): int;

    public function updateRules(Request $input, string $lanId): array;

    public function updateLanName(Request $input, string $lanId): array;

    public function updateLanPrice(Request $input, string $lanId): array;

    public function updateLanLocation(Request $input, string $lanId): array;

    public function updateLanSeatReservationStart(Request $input, string $lanId): array;

    public function updateLanTournamentReservationStart(Request $input, string $lanId): array;

    public function updateLanStartDate(Request $input, string $lanId): array;

    public function updateLanEndDate(Request $input, string $lanId): array;

    public function updateLanSeatsKeys(Request $input, string $lanId): array;

    public function updateLanDescription(Request $input, string $lanId): array;

    public function updateLanPlaces(Request $input, string $lanId): array;
}