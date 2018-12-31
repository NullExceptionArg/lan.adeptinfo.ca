<?php


namespace App\Services\Implementation;

use App\Http\Resources\User\GetAdminRolesResource;
use App\Http\Resources\User\GetAdminSummaryResource;
use App\Http\Resources\User\GetUserCollection;
use App\Http\Resources\User\GetUserDetailsResource;
use App\Http\Resources\User\GetUserSummaryResource;
use App\Mail\ConfirmAccount;
use App\Model\User;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\RoleRepositoryImpl;
use App\Repositories\Implementation\SeatRepositoryImpl;
use App\Repositories\Implementation\TeamRepositoryImpl;
use App\Repositories\Implementation\TournamentRepositoryImpl;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Rules\FacebookEmailPermission;
use App\Rules\HasPermission;
use App\Rules\HasPermissionInLan;
use App\Rules\UniqueEmailSocialLogin;
use App\Rules\ValidFacebookToken;
use App\Rules\ValidGoogleToken;
use App\Services\UserService;
use Google_Client;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserServiceImpl implements UserService
{
    protected $userRepository;
    protected $seatRepository;
    protected $lanRepository;
    protected $teamRepository;
    protected $roleRepository;
    protected $tournamentRepository;

    /**
     * UserServiceImpl constructor.
     * @param UserRepositoryImpl $userRepository
     * @param SeatRepositoryImpl $seatRepository
     * @param LanRepositoryImpl $lanRepository
     * @param TeamRepositoryImpl $teamRepository
     * @param RoleRepositoryImpl $roleRepository
     * @param TournamentRepositoryImpl $tournamentRepository
     */
    public function __construct(
        UserRepositoryImpl $userRepository,
        SeatRepositoryImpl $seatRepository,
        LanRepositoryImpl $lanRepository,
        TeamRepositoryImpl $teamRepository,
        RoleRepositoryImpl $roleRepository,
        TournamentRepositoryImpl $tournamentRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->seatRepository = $seatRepository;
        $this->lanRepository = $lanRepository;
        $this->teamRepository = $teamRepository;
        $this->roleRepository = $roleRepository;
        $this->tournamentRepository = $tournamentRepository;
    }

    public function signUpUser(Request $input): User
    {
        $userValidator = Validator::make($input->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => ['required', 'email', new UniqueEmailSocialLogin],
            'password' => 'required|min:6|max:20',
        ]);

        if ($userValidator->fails()) {
            throw new BadRequestHttpException($userValidator->errors());
        };

        $user = $this->userRepository->findByEmail($input->input('email'));
        $confirmationCode = str_random(30);

        if ($user != null) {
            $this->userRepository->addConfirmationCode($user, $confirmationCode);
        } else {
            $user = $this->userRepository->createUser(
                $input->input('first_name'),
                $input->input('last_name'),
                $input->input('email'),
                $input->input('password'),
                $confirmationCode
            );
        }

        Mail::send(new ConfirmAccount(
            $input->input('email'),
            $confirmationCode,
            $user->first_name
        ));

        return $user;
    }

    public function logOut(): void
    {
        $accessToken = Auth::user()->token();

        $this->userRepository->revokeRefreshToken($accessToken);
        $this->userRepository->revokeAccessToken($accessToken);
    }

    public function deleteUser(): void
    {
        $user = Auth::user();
        $this->userRepository->deleteUserById($user->id);
    }

    public function getUsers(Request $request): GetUserCollection
    {
        $userValidator = Validator::make($request->all(), [
            'query_string' => 'max:255|string',
            'order_column' => [Rule::in(['first_name', 'last_name', 'email']),],
            'order_direction' => [Rule::in(['asc', 'desc']),],
            'items_per_page' => 'numeric|min:1|max:75',
            'current_page' => 'numeric|min:1'
        ]);

        if ($userValidator->fails()) {
            throw new BadRequestHttpException($userValidator->errors());
        }

        // Default order column: last_name
        if ($request->input('order_column') == null) {
            $request['order_column'] = 'last_name';
        }

        // Default order direction: asc
        if ($request->input('order_direction') == null) {
            $request['order_direction'] = 'asc';
        }

        // Default items per page: 15
        if ($request->input('items_per_page') == null) {
            $request['items_per_page'] = 15;
        }

        // Default current page: 1
        if ($request->input('current_page') == null) {
            $request['current_page'] = 1;
        }

        return new GetUserCollection($this->userRepository->getPaginatedUsersCriteria(
            $request->input('query_string'),
            $request->input('order_column'),
            $request->input('order_direction'),
            $request->input('items_per_page'),
            $request->input('current_page')
        ));
    }

    public function getUserDetails(Request $input): GetUserDetailsResource
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $userValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'email' => $input->input('email')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'email' => 'required|exists:user,email',
        ]);

        if ($userValidator->fails()) {
            throw new BadRequestHttpException($userValidator->errors());
        }

        $user = $this->userRepository->findByEmail($input->input('email'));
        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        $currentSeat = $this->seatRepository->getCurrentSeat($user->id, $lan->id);
        $seatHistory = $this->seatRepository->getSeatHistoryForUser($user->id, $lan->id);

        return new GetUserDetailsResource($user, $currentSeat, $seatHistory);
    }

    public function signInFacebook(Request $input): array
    {
        $userValidator = Validator::make([
            'access_token' => $input->input('access_token'),
        ], [
            'access_token' => [new ValidFacebookToken, new FacebookEmailPermission]
        ]);

        if ($userValidator->fails()) {
            throw new BadRequestHttpException($userValidator->errors());
        }

        $facebookUser = null;
        $client = new Client([
            'base_uri' => 'https://graph.facebook.com',
            'timeout' => 2.0]);
        $facebookUser = \GuzzleHttp\json_decode($client->get('/me', ['query' => [
            'fields' => 'id,first_name,last_name,email',
            'access_token' => $input->input('access_token')
        ]])->getBody());


        $user = $this->userRepository->findByEmail($facebookUser->email);
        $isNew = $user == null;
        if ($isNew) {
            $user = $this->userRepository->createFacebookUser(
                $facebookUser->id,
                $facebookUser->first_name,
                $facebookUser->last_name,
                $facebookUser->email
            );
        }

        if ($user->facebook_id == null) {
            $user = $this->userRepository->addFacebookToUser($user, $facebookUser->id);
        }

        $token = $user->createToken('facebook')->accessToken;
        return [
            'token' => $token,
            'is_new' => $isNew
        ];
    }

    public function confirm(string $confirmationCode)
    {
        $confirmationValidator = Validator::make([
            'confirmation_code' => $confirmationCode,
        ], [
            'confirmation_code' => 'exists:user,confirmation_code'
        ]);

        if ($confirmationValidator->fails()) {
            throw new BadRequestHttpException($confirmationValidator->errors());
        }

        $user = $this->userRepository->findByConfirmationCode($confirmationCode);
        $this->userRepository->confirmAccount($user);
    }

    public function signInGoogle(Request $input): array
    {
        $userValidator = Validator::make([
            'access_token' => $input->input('access_token'),
        ], [
            'access_token' => [new ValidGoogleToken()]
        ]);

        if ($userValidator->fails()) {
            throw new BadRequestHttpException($userValidator->errors());
        }

        $client = new Google_Client();
        $client->setApplicationName('LAN de l\'ADEPT');
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $googleResult = $client->verifyIdToken($input->input('access_token'));

        $user = $this->userRepository->findByEmail($googleResult['email']);
        $isNew = $user == null;
        if ($isNew) {
            $user = $this->userRepository->createGoogleUser(
                $googleResult['sub'],
                $googleResult['given_name'],
                $googleResult['family_name'],
                $googleResult['email']
            );
        }

        if ($user->facebook_id == null) {
            $user = $this->userRepository->addGoogleToUser($user, $googleResult['sub']);
        }

        $token = $user->createToken('google')->accessToken;
        return [
            'token' => $token,
            'is_new' => $isNew
        ];
    }

    public function getUserSummary(Request $input): GetUserSummaryResource
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $userValidator = Validator::make([
            'lan_id' => $input->input('lan_id')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL'
        ]);

        if ($userValidator->fails()) {
            throw new BadRequestHttpException($userValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        $user = Auth::user();
        return new GetUserSummaryResource($user, $this->teamRepository->getLeadersRequestTotalCount($user, $lan));
    }

    public function getAdminSummary(Request $input): GetAdminSummaryResource
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $userValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'permission' => 'admin-summary'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'permission' => new HasPermissionInLan($input->input('lan_id'), Auth::id())
        ]);

        if ($userValidator->fails()) {
            throw new BadRequestHttpException($userValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        $user = Auth::user();
        $permissions = $this->roleRepository->getAdminPermissions($lan, $user);

        $hasTournaments =
            ($this->roleRepository->userHasPermission('edit-tournament', $user->id, $lan->id) &&
                $this->roleRepository->userHasPermission('delete-tournament', $user->id, $lan->id) &&
                $this->roleRepository->userHasPermission('add-organizer', $user->id, $lan->id)) ||
            $this->tournamentRepository->adminHasTournaments($user->id, $lan->id);

        return new GetAdminSummaryResource($user, $hasTournaments, $permissions);
    }

    public function getAdminRoles(Request $input)
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        if (is_null($input->input('email'))) {
            $input['email'] = Auth::user()->email;
        }

        $userValidator = Validator::make([
            'email' => $input->input('email'),
            'lan_id' => $input->input('lan_id'),
            'permission' => 'get-admin-roles'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'email' => 'string|exists:user,email'
        ]);

        $userValidator->sometimes('permission', [new HasPermission(Auth::id())], function ($input) {
            return Auth::user()->email != $input['email'];
        });

        if ($userValidator->fails()) {
            throw new BadRequestHttpException($userValidator->errors());
        }

        $globalRoles = $this->roleRepository->getUsersGlobalRoles($input['email']);
        $lanRoles = $this->roleRepository->getUsersLanRoles($input['email'], $input->input('lan_id'));

        return new GetAdminRolesResource($globalRoles, $lanRoles);
    }
}