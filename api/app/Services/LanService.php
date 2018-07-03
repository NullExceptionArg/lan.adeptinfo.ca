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

    public function updateRules(Request $input, string $lanId): array;

    public function getLans(): ResourceCollection;
}