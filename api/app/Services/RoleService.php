<?php

namespace App\Services;


use App\Model\GlobalRole;
use App\Model\LanRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

interface RoleService
{
    public function createLanRole(Request $request): LanRole;

    public function editLanRole(Request $input): LanRole;

    public function assignLanRole(Request $input): LanRole;

    public function addPermissionsLanRole(Request $input): LanRole;

    public function getLanRoles(Request $input): Collection;

    public function getLanRolePermissions(Request $input): AnonymousResourceCollection;

    public function createGlobalRole(Request $request): GlobalRole;

    public function editGlobalRole(Request $input): GlobalRole;

    public function assignGlobalRole(Request $input): GlobalRole;

    public function addPermissionsGlobalRole(Request $input): GlobalRole;

    public function getGlobalRoles(Request $input): Collection;

    public function getGlobalRolePermissions(Request $input): AnonymousResourceCollection;
}