<?php

namespace App\Http\Controllers;

use App\Rules\{User\FacebookEmailPermission,
    User\HasPermissionInLan,
    User\UniqueEmailSocialLogin,
    User\ValidFacebookToken,
    User\ValidGoogleToken};
use App\Services\Implementation\UserServiceImpl;
use Illuminate\{Http\Request, Support\Facades\Auth, Support\Facades\Validator, Validation\Rule};

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

    public function confirm(Request $request, string $confirmationCode)
    {
        $validator = Validator::make([
            'confirmation_code' => $confirmationCode,
        ], [
            'confirmation_code' => 'exists:user,confirmation_code'
        ]);

        $this->checkValidation($validator);

        $this->userService->confirm($confirmationCode);
        return response()->json([], 200);
    }

    public function createTag(Request $request)
    {
        $validator = Validator::make([
            'name' => $request->input('name'),
        ], [
            'name' => 'required|string|max:5|unique:tag,name',
        ]);

        $this->checkValidation($validator);

        return response()->json($this->userService->createTag(
            $request->input('name')
        ), 201);
    }

    public function deleteUser()
    {
        $this->userService->deleteUser();
        return response()->json([], 200);
    }

    public function getAdminRoles(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $request = $this->adjustRequestForEmail($request);
        $validator = Validator::make([
            'email' => $request->input('email'),
            'lan_id' => $request->input('lan_id'),
            'permission' => 'get-admin-roles'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'email' => 'string|exists:user,email'
        ]);

        $validator->sometimes('permission', [new HasPermissionInLan($request->input('lan_id'), Auth::id())], function ($request) {
            return Auth::user()->email != $request['email'];
        });

        $this->checkValidation($validator);

        return response()->json($this->userService->getAdminRoles(
            $request->input('email'),
            $request->input('lan_id')
        ), 200);
    }

    public function getAdminSummary(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'permission' => 'admin-summary'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->userService->getAdminSummary(
            $request->input('lan_id')
        ), 200);
    }

    public function getUserDetails(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'email' => $request->input('email')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'email' => 'required|exists:user,email',
        ]);

        $this->checkValidation($validator);

        return response()->json($this->userService->getUserDetails(
            $request->input('lan_id'),
            $request->input('email')
        ), 200);
    }

    public function getUsers(Request $request)
    {
        if ($request->input('items_per_page') === '') {
            $request['items_per_page'] = null;
        }

        if ($request->input('current_page') === '') {
            $request['current_page'] = null;
        }

        $validator = Validator::make($request->all(), [
            'query_string' => 'max:255|string',
            'order_column' => [Rule::in(['first_name', 'last_name', 'email']),],
            'order_direction' => [Rule::in(['asc', 'desc']),],
            'items_per_page' => 'integer|nullable|min:1|max:75',
            'current_page' => 'integer|nullable|min:1'
        ]);

        $this->checkValidation($validator);

        return response()->json($this->userService->getUsers(
            $request->input('query_string'),
            $request->input('order_column'),
            $request->input('order_direction'),
            $request->input('items_per_page'),
            $request->input('current_page')
        ), 200);
    }

    public function getUserSummary(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL'
        ]);

        $this->checkValidation($validator);

        return response()->json($this->userService->getUserSummary(
            $request->input('lan_id')
        ), 200);
    }

    public function logOut()
    {
        $this->userService->logOut();
        return response()->json([], 200);
    }

    public function signInFacebook(Request $request)
    {
        $validator = Validator::make([
            'access_token' => $request->input('access_token'),
        ], [
            'access_token' => [new ValidFacebookToken, new FacebookEmailPermission]
        ]);

        $this->checkValidation($validator);

        $response = $this->userService->signInFacebook($request->input('access_token'));
        return response()->json(['token' => $response['token']], $response['is_new'] ? 201 : 200);
    }

    public function signInGoogle(Request $request)
    {
        $validator = Validator::make([
            'access_token' => $request->input('access_token'),
        ], [
            'access_token' => [new ValidGoogleToken]
        ]);

        $this->checkValidation($validator);

        $response = $this->userService->signInGoogle($request->input('access_token'));
        return response()->json(['token' => $response['token']], $response['is_new'] ? 201 : 200);
    }

    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => ['required', 'email', new UniqueEmailSocialLogin],
            'password' => 'required|min:6|max:20',
        ]);

        $this->checkValidation($validator);

        return response()->json($this->userService->signUpUser(
            $request->input('first_name'),
            $request->input('last_name'),
            $request->input('email'),
            $request->input('password')
        ), 201);
    }
}
