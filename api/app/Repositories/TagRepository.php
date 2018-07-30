<?php

namespace App\Repositories;


use App\Model\Tag;
use Illuminate\Contracts\Auth\Authenticatable;

interface TagRepository
{
    public function create(
        Authenticatable $user,
        string $name
    ): Tag;

    public function findTagById(int $id): ?Tag;
}