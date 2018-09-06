<?php

namespace App\Repositories;


use App\Model\Role;

interface RoleRepository
{
    public function create(
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): Role;
}