<?php

namespace App\Services;


use App\Http\Resources\Role\GetRoleResource;
use App\Model\GlobalRole;
use App\Model\LanRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

interface RoleService
{
    public function createLanRole(Request $request): LanRole;

    public function editLanRole(Request $input): LanRole;

    public function assignLanRole(Request $input): GetRoleResource;

    public function addPermissionsLanRole(Request $input): GetRoleResource;

    public function deletePermissionsLanRole(Request $input): GetRoleResource;

    public function deleteLanRole(Request $input): GetRoleResource;

    public function getLanRoles(Request $input): AnonymousResourceCollection;

    public function getLanRolePermissions(Request $input): AnonymousResourceCollection;

    public function getLanUsers(Request $input): Collection;

    public function createGlobalRole(Request $request): GlobalRole;

    public function editGlobalRole(Request $input): GlobalRole;

    public function assignGlobalRole(Request $input): GetRoleResource;

    public function addPermissionsGlobalRole(Request $input): GetRoleResource;

    public function deletePermissionsGlobalRole(Request $input): GetRoleResource;

    public function deleteGlobalRole(Request $input): GetRoleResource;

    public function getGlobalRoles(Request $input): AnonymousResourceCollection;

    public function getGlobalRolePermissions(Request $input): AnonymousResourceCollection;

    public function getGlobalUsers(Request $input): Collection;

    public function getPermissions(): AnonymousResourceCollection;
}