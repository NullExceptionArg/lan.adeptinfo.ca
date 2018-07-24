<?php

namespace App\Repositories\Implementation;

use App\Model\Tag;
use App\Repositories\TagRepository;
use Illuminate\Contracts\Auth\Authenticatable;

class TagRepositoryImpl implements TagRepository
{
    // TODO Tests
    public function create(
        Authenticatable $user,
        string $name
    ): Tag
    {
        $tag = new Tag();
        $tag->name = $name;
        $tag->user_id = $user->id;
        $tag->save();

        return $tag;
    }
}