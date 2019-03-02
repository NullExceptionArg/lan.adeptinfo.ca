<?php

namespace App\Http\Controllers;

use App\Model\Lan;
use App\Rules\{User\FacebookEmailPermission,
    User\HasPermissionInLan,
    User\UniqueEmailSocialLogin,
    User\ValidFacebookToken,
    User\ValidGoogleToken};
use App\Services\Implementation\UserServiceImpl;
use Illuminate\{Http\Request, Support\Facades\Auth, Support\Facades\Validator, Validation\Rule};

/**
 * Validation et application de la logique applicative sur les utilisateurs de l'application.
 *
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Service d'utilisateur.
     *
     * @var UserServiceImpl
     */
    protected $userService;

    /**
     * UserController constructor.
     * @param UserServiceImpl $userService
     */
    public function __construct(UserServiceImpl $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#confirmer-un-compte
     * @param Request $request
     * @param string $confirmationCode
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#creer-un-tag
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTag(Request $request)
    {
        $validator = Validator::make([
            'name' => $request->input('name'),
        ], [
            'name' => 'required|string|max:5|unique:tag,name',
        ]);

        $this->checkValidation($validator);

        return response()->json($this->userService->createTag(
            Auth::id(),
            $request->input('name')
        ), 201);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#supprimer-l-39-utilisateur
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser()
    {
        $this->userService->deleteUser(Auth::id());
        return response()->json([], 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#roles-d-39-un-administrateur
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#sommaire-de-l-39-administrateur
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
            Auth::id(),
            $request->input('lan_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#details-d-39-un-utilisateur
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserDetails(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'email' => $request->input('email'),
            'permission' => 'get-user-details'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'email' => 'required|exists:user,email',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->userService->getUserDetails(
            $request->input('lan_id'),
            $request->input('email')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#lister-les-utilisateurs
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request)
    {
        // Correction des champs de la requête qui sont utilisés comme integer, puisque '' == 0 est true en PHP...
        if ($request->input('items_per_page') === '') {
            $request['items_per_page'] = null;
        }

        if ($request->input('current_page') === '') {
            $request['current_page'] = null;
        }

        $validator = Validator::make([
            'query_string' => $request->input('query_string'),
            'order_column' => $request->input('order_column'),
            'order_direction' => $request->input('order_direction'),
            'items_per_page' => $request->input('items_per_page'),
            'current_page' => $request->input('current_page'),
            'permission' => 'get-users'
        ], [
            'query_string' => 'max:255|string',
            'order_column' => [Rule::in(['first_name', 'last_name', 'email']),],
            'order_direction' => [Rule::in(['asc', 'desc']),],
            'items_per_page' => 'integer|nullable|min:1|max:75',
            'current_page' => 'integer|nullable|min:1',
            'permission' => new HasPermissionInLan(Lan::getCurrent()->id, Auth::id())
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

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#sommaire-de-l-39-utilisateur
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
            Auth::id(),
            $request->input('lan_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#deconnexion
     * @return \Illuminate\Http\JsonResponse
     */
    public function logOut()
    {
        $this->userService->logOut();
        return response()->json([], 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#connexion-avec-facebook
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#connexion-avec-google
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#creer-un-compte-utilisateur
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
