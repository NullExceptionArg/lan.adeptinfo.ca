<?php


namespace App\Services;

use App\Http\Resources\Lan\GetResource;
use App\Http\Resources\Lan\UpdateResource;
use App\Model\Lan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

interface LanService
{
    public function create(Request $input): Lan;

    public function get(Request $request): GetResource;

    public function getAll(): ResourceCollection;

    public function setCurrent(Request $input): int;

    public function edit(Request $input): UpdateResource;
}