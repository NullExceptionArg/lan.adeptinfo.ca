<?php

namespace App\Http\Controllers;

use App\Services\Implementation\UserServiceImpl;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    /**
     * UserController constructor.
     * @param UserServiceImpl $userService
     */
    public function __construct(UserServiceImpl $userService)
    {
        $this->userService = $userService;
    }

    public function signUp(Request $request){
        return response()->json($this->userService->signUp($request), 201);
    }

}
