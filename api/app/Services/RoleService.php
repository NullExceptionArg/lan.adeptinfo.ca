<?php

namespace App\Services;


use App\Model\Role;
use Illuminate\Http\Request;

interface RoleService
{
    public function create(Request $request): Role;
}