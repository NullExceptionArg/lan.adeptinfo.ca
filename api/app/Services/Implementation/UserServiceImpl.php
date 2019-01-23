<?php

namespace App\Services\Implementation;

use App\Http\Resources\User\GetAdminRolesResource;
use App\Http\Resources\User\GetAdminSummaryResource;
use App\Http\Resources\User\GetUserCollection;
use App\Http\Resources\User\GetUserDetailsResource;
use App\Http\Resources\User\GetUserSummaryResource;
use App\Mail\ConfirmAccount;
use App\Model\Tag;
use App\Model\User;
use App\Repositories\Implementation\RoleRepositoryImpl;
use App\Repositories\Implementation\SeatRepositoryImpl;
use App\Repositories\Implementation\TeamRepositoryImpl;
use App\Repositories\Implementation\TournamentRepositoryImpl;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Services\UserService;
use App\Utils\FacebookUtils;
use Facebook\Exceptions\FacebookSDKException;
use Google_Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserServiceImpl implements UserService
{
    protected $userRepository;
    protected $seatRepository;
    protected $teamRepository;
    protected $roleRepository;
    protected $tournamentRepository;

    /**
     * UserServiceImpl constructor.
     * @param UserRepositoryImpl $userRepository
     * @param SeatRepositoryImpl $seatRepository
     * @param TeamRepositoryImpl $teamRepository
     * @param RoleRepositoryImpl $roleRepository
     * @param TournamentRepositoryImpl $tournamentRepository
     */
    public function __construct(
        UserRepositoryImpl $userRepository,
        SeatRepositoryImpl $seatRepository,
        TeamRepositoryImpl $teamRepository,
        RoleRepositoryImpl $roleRepository,
        TournamentRepositoryImpl $tournamentRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->seatRepository = $seatRepository;
        $this->teamRepository = $teamRepository;
        $this->roleRepository = $roleRepository;
        $this->tournamentRepository = $tournamentRepository;
    }

    public function confirm(string $confirmationCode): void
    {
        $user = $this->userRepository->findByConfirmationCode($confirmationCode);
        $this->userRepository->confirmAccount($user->id);
    }

    public function createTag(string $name): Tag
    {
        $tagId = $this->userRepository->createTag(Auth::id(), $name);
        return $this->userRepository->findTagById($tagId);
    }

    public function deleteUser(): void
    {
        $this->userRepository->deleteUserById(Auth::id());
    }

    public function getAdminRoles(string $email, int $lanId): GetAdminRolesResource
    {
        $globalRoles = $this->roleRepository->getUsersGlobalRoles($email);
        $lanRoles = $this->roleRepository->getUsersLanRoles($email, $lanId);

        return new GetAdminRolesResource($globalRoles, $lanRoles);
    }

    public function getAdminSummary(int $lanId): GetAdminSummaryResource
    {
        $user = Auth::user();
        $permissions = $this->roleRepository->getAdminPermissions($lanId, $user->id);

        $hasTournaments =
            ($this->roleRepository->userHasPermission('edit-tournament', $user->id, $lanId) &&
                $this->roleRepository->userHasPermission('delete-tournament', $user->id, $lanId) &&
                $this->roleRepository->userHasPermission('add-organizer', $user->id, $lanId)) ||
            $this->tournamentRepository->adminHasTournaments($user->id, $lanId);

        return new GetAdminSummaryResource($user, $hasTournaments, $permissions);
    }

    public function getUserDetails(int $lanId, string $email): GetUserDetailsResource
    {
        $user = $this->userRepository->findByEmail($email);
        $currentSeat = $this->seatRepository->findReservationByLanIdAndUserId($user->id, $lanId);
        $seatHistory = $this->seatRepository->getSeatHistoryForUser($user->id, $lanId);

        return new GetUserDetailsResource($user, $currentSeat, $seatHistory);
    }

    public function getUsers(
        ?string $queryString,
        ?string $orderColumn,
        ?string $orderDirection,
        ?int $itemsPerPage,
        ?int $currentPage
    ): GetUserCollection
    {
        // Default query string: '
        if (is_null($queryString)) {
            $queryString = '';
        }

        // Default order column: last_name
        if (is_null($orderColumn)) {
            $orderColumn = 'last_name';
        }

        // Default order direction: asc
        if (is_null($orderDirection)) {
            $orderDirection = 'asc';
        }

        // Default items per page: 15
        if (is_null($itemsPerPage)) {
            $itemsPerPage = 15;
        }

        // Default current page: 1
        if (is_null($currentPage)) {
            $currentPage = 1;
        }

        return new GetUserCollection($this->userRepository->getPaginatedUsersCriteria(
            $queryString,
            $orderColumn,
            $orderDirection,
            $itemsPerPage,
            $currentPage
        ));
    }

    public function getUserSummary(int $lanId): GetUserSummaryResource
    {
        $user = Auth::user();
        return new GetUserSummaryResource($user, $this->teamRepository->getLeadersRequestTotalCount($user->id, $lanId));
    }

    public function logOut(): void
    {
        $accessToken = Auth::user()->token();

        $this->userRepository->revokeRefreshToken($accessToken);
        $accessToken->revoke();
    }

    public function signInFacebook(string $accessToken): array
    {
        $facebookUser = null;
        try {
            $facebookUser = FacebookUtils::getFacebook()->get(
                '/me?fields=id,first_name,last_name,email',
                $accessToken
            )->getDecodedBody();
        } catch (FacebookSDKException $e) {
            exit(500);
        }

        $user = $this->userRepository->findByEmail($facebookUser['email']);
        $isNew = is_null($user);
        if ($isNew) {
            $userId = $this->userRepository->createFacebookUser(
                $facebookUser['id'],
                $facebookUser['first_name'],
                $facebookUser['last_name'],
                $facebookUser['email']
            );
            $user = $this->userRepository->findById($userId);
        }

        if ($user->facebook_id == null) {
            $this->userRepository->addFacebookToUser($user->email, $facebookUser['id']);
        }

        $token = $user->createToken('facebook')->accessToken;
        return [
            'token' => $token,
            'is_new' => $isNew
        ];
    }

    public function signInGoogle(string $accessToken): array
    {
        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $googleResult = $client->verifyIdToken($accessToken);

        $user = $this->userRepository->findByEmail($googleResult['email']);
        $isNew = $user == null;
        if ($isNew) {
            $userId = $this->userRepository->createGoogleUser(
                $googleResult['sub'],
                $googleResult['given_name'],
                $googleResult['family_name'],
                $googleResult['email']
            );
            $user = $this->userRepository->findById($userId);
        }

        if (is_null($user->google_id)) {
            $this->userRepository->addGoogleToUser($user->email, $googleResult['sub']);
        }

        $token = $user->createToken('google')->accessToken;
        return [
            'token' => $token,
            'is_new' => $isNew
        ];
    }

    public function signUpUser(string $firstName, string $lastName, string $email, string $password): User
    {
        $user = $this->userRepository->findByEmail($email);
        $confirmationCode = str_random(30);

        if (!is_null($user)) {
            $this->userRepository->addConfirmationCode($user->email, $confirmationCode);
        } else {
            $userId = $this->userRepository->createUser(
                $firstName,
                $lastName,
                $email,
                $password,
                $confirmationCode
            );
            $user = $this->userRepository->findById($userId);
        }

        Mail::send(new ConfirmAccount(
            $email,
            $confirmationCode,
            $user->first_name
        ));

        return $user;
    }
}
