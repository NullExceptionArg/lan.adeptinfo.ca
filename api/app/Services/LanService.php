<?php


namespace App\Services;

use App\Http\Resources\Lan\LanResource;
use App\Model\Lan;
use Illuminate\Http\Request;

interface LanService
{
    public function createLan(Request $input): Lan;

    public function getLan(Request $request, string $lanId): LanResource;

    public function updateRules(Request $input, string $lanId): array;

}