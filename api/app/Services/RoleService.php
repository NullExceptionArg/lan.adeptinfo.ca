<?php

namespace App\Services;


use App\Model\GlobalRole;
use App\Model\LanRole;
use Illuminate\Http\Request;

interface RoleService
{
    public function createLanRole(Request $request): LanRole;

    public function createGlobalRole(Request $request): GlobalRole;
}