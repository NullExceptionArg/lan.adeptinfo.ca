<?php

namespace App\Repositories\Implementation;


use App\Model\Role;
use App\Repositories\RoleRepository;

class RoleRepositoryImpl implements RoleRepository
{
    public function create(string $name,
                           string $enDisplayName,
                           string $enDescription,
                           string $frDisplayName,
                           string $frDescription
    ): Role
    {
        $role = new Role();
        $role->name = $name;
        $role->enDisplayName = $enDisplayName;
        $role->enDescription = $enDescription;
        $role->frDisplayName = $frDisplayName;
        $role->frDescription = $frDescription;
        $role->save();

        return $role;
    }
}