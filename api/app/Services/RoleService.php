<?php

namespace App\Services;


use App\Model\GlobalRole;
use App\Model\LanRole;
use Illuminate\Http\Request;

interface RoleService
{
    public function createLanRole(Request $request): LanRole;

    public function editLanRole(Request $input): LanRole;

    public function assignLanRole(Request $input): LanRole;

    public function addPermissionsLanRole(Request $input): LanRole;

    public function createGlobalRole(Request $request): GlobalRole;

    public function editGlobalRole(Request $input): GlobalRole;

    public function assignGlobalRole(Request $input): GlobalRole;

    public function addPermissionsGlobalRole(Request $input): GlobalRole;
}