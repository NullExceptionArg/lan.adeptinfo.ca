<?php

namespace App\Repositories;


use App\Model\Tag;
use App\Model\User;
use Illuminate\Contracts\Auth\Authenticatable;

interface TagRepository
{
    public function create(
        Authenticatable $user,
        string $name
    ): Tag;
}