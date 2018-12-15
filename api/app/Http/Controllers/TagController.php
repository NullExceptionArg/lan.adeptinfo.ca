<?php

namespace App\Http\Controllers;


use App\Services\Implementation\TagServiceImpl;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $tagServiceImpl;

    /**
     * TagController constructor.
     * @param TagServiceImpl $tagServiceImpl
     */
    public function __construct(TagServiceImpl $tagServiceImpl)
    {
        $this->tagServiceImpl = $tagServiceImpl;
    }

    public function createTag(Request $request)
    {
        return response()->json($this->tagServiceImpl->create($request), 201);
    }

}