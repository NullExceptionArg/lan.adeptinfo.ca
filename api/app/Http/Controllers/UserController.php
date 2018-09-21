<?php

namespace App\Http\Controllers;

use App\Services\Implementation\UserServiceImpl;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use Helpers;

    protected $userService;

    /**
     * UserController constructor.
     * @param UserServiceImpl $userService
     */
    public function __construct(UserServiceImpl $userService)
    {
        $this->userService = $userService;
    }

    public function signUp(Request $request)
    {
        return response()->json($this->userService->signUpUser($request), 201);
    }

    public function signInFacebook(Request $request)
    {
        $response = $this->userService->signInFacebook($request);
        return response()->json(['token' => $response['token']], $response['is_new'] ? 201 : 200);
    }

    public function signInGoogle(Request $request)
    {
        $response = $this->userService->signInGoogle($request);
        return response()->json(['token' => $response['token']], $response['is_new'] ? 201 : 200);
    }

    public function logOut()
    {
        $this->userService->logOut();
        return response()->json([], 200);
    }

    public function deleteUser()
    {
        $this->userService->deleteUser();
        return response()->json([], 200);
    }

    public function getUsers(Request $request)
    {
        return response()->json($this->userService->getUsers($request), 200);
    }

    public function getUserDetails(Request $request)
    {
        return response()->json($this->userService->getUserDetails($request), 200);
    }

    public function getUserSummary(Request $request)
    {
        return response()->json($this->userService->getUserSummary($request), 200);
    }

    public function getAdminSummary(Request $request)
    {
        // TODO Permissions admin-summary
        return response()->json($this->userService->getAdminSummary($request), 200);
    }

}
