<?php

namespace App\Services\Implementation;


use App\Repositories\Implementation\TagRepositoryImpl;
use App\Services\TagService;

class TagServiceImpl implements TagService
{
    protected $tagRepository;

    /**
     * LanServiceImpl constructor.
     * @param TagRepositoryImpl $tagRepositoryImpl
     */
    public function __construct(TagRepositoryImpl $tagRepositoryImpl)
    {
        $this->tagRepository = $tagRepositoryImpl;
    }


}