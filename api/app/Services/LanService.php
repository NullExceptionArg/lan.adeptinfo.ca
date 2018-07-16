<?php


namespace App\Services;

use App\Http\Resources\Lan\GetLanResource;
use App\Http\Resources\Lan\UpdateLanResource;
use App\Model\Lan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

interface LanService
{
    public function createLan(Request $input): Lan;

    public function getLan(Request $request): GetLanResource;

    public function getLans(): ResourceCollection;

    public function setCurrentLan(string $lanId): int;

    public function update(Request $input, string $lanId): UpdateLanResource;
}