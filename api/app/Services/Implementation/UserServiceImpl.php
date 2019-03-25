<?php

namespace App\Services\Implementation;

use App\Http\Resources\{User\GetAdminRolesResource,
    User\GetAdminSummaryResource,
    User\GetUserCollection,
    User\GetUserDetailsResource,
    User\GetUserSummaryResource};
use App\Mail\ConfirmAccount;
use App\Model\{Tag, User};
use App\Repositories\Implementation\{RoleRepositoryImpl,
    SeatRepositoryImpl,
    TeamRepositoryImpl,
    TournamentRepositoryImpl,
    UserRepositoryImpl};
use App\Services\UserService;
use App\Utils\FacebookUtils;
use Facebook\Exceptions\FacebookSDKException;
use Google_Client;
use Illuminate\{Support\Facades\Auth, Support\Facades\Mail};

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
        // Trouver l'utilisateur qui correspond au code de confirmation
        $user = $this->userRepository->findByConfirmationCode($confirmationCode);

        // Confirmer le compte de l'utilisateur
        $this->userRepository->confirmAccount($user->id);
    }

    public function createTag(int $userId, string $name): Tag
    {
        // Créer le tag
        $tagId = $this->userRepository->createTag($userId, $name);

        // Trouver et retourner le le tag créé
        return $this->userRepository->findTagById($tagId);
    }

    public function deleteUser(int $userId): void
    {
        // Supprimer l'utilisateur
        $this->userRepository->deleteUserById($userId);
    }

    public function getAdminRoles(string $email, int $lanId): GetAdminRolesResource
    {
        // Trouver les rôles globaux de l'utilisateur
        $globalRoles = $this->roleRepository->getUsersGlobalRoles($email);

        // Trouver les rôles de LAN de l'utilisateur
        $lanRoles = $this->roleRepository->getUsersLanRoles($email, $lanId);

        // Retourner les rôles de l'utilisateur
        return new GetAdminRolesResource($globalRoles, $lanRoles);
    }

    public function getAdminSummary(int $userId, ?int $lanId): GetAdminSummaryResource
    {
        // Trouver l'utilisateur
        $user = $this->userRepository->findById($userId);

        // Trouver les permissions de l'utilisateur
        $permissions = $this->roleRepository->getAdminPermissions($lanId, $user->id);

        // Déterminer si l'utilisateur possède des tournois
        $hasTournaments =
            ($this->roleRepository->userHasPermission('edit-tournament', $user->id, $lanId) &&
                $this->roleRepository->userHasPermission('delete-tournament', $user->id, $lanId) &&
                $this->roleRepository->userHasPermission('add-organizer', $user->id, $lanId)) ||
            $this->tournamentRepository->adminHasTournaments($user->id, $lanId);

        // Retourner les détails de l'administrateur
        return new GetAdminSummaryResource($user, $hasTournaments, $permissions);
    }

    public function getUserDetails(int $lanId, string $email): GetUserDetailsResource
    {
        // Trouver l'utilisateur qui correspond au courriel
        $user = $this->userRepository->findByEmail($email);

        // Obtenir le siège courant de l'utilisateur dans le LAN
        $currentSeat = $this->seatRepository->findReservationByLanIdAndUserId($user->id, $lanId);

        // Obtenir l'historique des places qu'a occupé l'utilisateur dans le LAN
        $seatHistory = $this->seatRepository->getSeatHistoryForUser($user->id, $lanId);

        // Retourner les détails de l'utilisateur
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
        // Valeur par défaut de la chaine de recherche
        if (is_null($queryString)) {
            $queryString = '';
        }

        // Valeur par défaut de la colonne à utiliser pour ordonner les résultats
        if (is_null($orderColumn)) {
            $orderColumn = 'last_name';
        }

        // Valeur par défaut de l'ordre ascendant ou descendant du tri des résultats
        if (is_null($orderDirection)) {
            $orderDirection = 'asc';
        }

        // Valeur par défaut du nombre de résultats par page
        if (is_null($itemsPerPage)) {
            $itemsPerPage = 15;
        }

        // Valeur par défaut de la page courante
        if (is_null($currentPage)) {
            $currentPage = 1;
        }

        // Trouver et retourner les résultats de la recherche
        return new GetUserCollection($this->userRepository->getPaginatedUsersCriteria(
            $queryString,
            $orderColumn,
            $orderDirection,
            $itemsPerPage,
            $currentPage
        ));
    }

    public function getUserSummary(int $userId, int $lanId): GetUserSummaryResource
    {
        // Trouver l'utilisateur
        $user = $this->userRepository->findById($userId);

        // Trouver et retourner le sommaire de l'utilisateur
        return new GetUserSummaryResource(
            $user,
            $this->teamRepository->getLeadersRequestTotalCount($user->id, $lanId)
        );
    }

    public function logOut(): void
    {
        // Trouver le token d'accès de l'utilisateur courant
        $accessToken = Auth::user()->token();

        // Révoquer le token de rafraichissement
        $this->userRepository->revokeRefreshToken($accessToken);

        // Révoquer le token d'accès
        $accessToken->revoke();
    }

    public function signInFacebook(string $accessToken): array
    {
        $facebookUser = null;
        try {
            // Obtenir l'utilisateur Facebook à partir du token
            $facebookUser = FacebookUtils::getFacebook()->get(
                '/me?fields=id,first_name,last_name,email',
                $accessToken
            )->getDecodedBody();
        } catch (FacebookSDKException $e) {
            exit(500);
        }

        // Trouver l'utilisateur (s'il existe) qui correspond au courriel de l'utilisateur Facebook
        $user = $this->userRepository->findByEmail($facebookUser['email']);

        // Déterminer si l'utilisateur est nouveau dans l'API
        $isNew = is_null($user);

        // Si l'utilisateur est nouveau
        if ($isNew) {
            // Créer un utilisateur
            $userId = $this->userRepository->createFacebookUser(
                $facebookUser['id'],
                $facebookUser['first_name'],
                $facebookUser['last_name'],
                $facebookUser['email']
            );

            // Trouver l'utilisateur créé
            $user = $this->userRepository->findById($userId);
        }

        // Si l'utisateur existe, mais qu'il ne s'est jamais connecté avec Facebook
        if (is_null($user->facebook_id)) {
            // Ajouter son id d'utilisateur Facebook
            $this->userRepository->addFacebookToUser($user->email, $facebookUser['id']);
        }

        // Créer un token d'accès à l'API
        $token = $user->createToken('facebook')->accessToken;

        // Retourner le token, et si l'uitilisateur est nouveau dans l'API
        return [
            'token' => $token,
            'is_new' => $isNew
        ];
    }

    public function signInGoogle(string $accessToken): array
    {
        // Créer un client Google
        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);

        // Obtenir l'utilisateur Google à partir du token
        $googleResult = $client->verifyIdToken($accessToken);

        // Trouver l'utilisateur (s'il existe) qui correspond au courriel de l'utilisateur Google
        $user = $this->userRepository->findByEmail($googleResult['email']);

        // Déterminer si l'utilisateur est nouveau dans l'API
        $isNew = is_null($user);

        // Si l'utilisateur est nouveau
        if ($isNew) {
            // Créer un utilisateur
            $userId = $this->userRepository->createGoogleUser(
                $googleResult['sub'],
                $googleResult['given_name'],
                $googleResult['family_name'],
                $googleResult['email']
            );

            // Trouver l'utilisateur créé
            $user = $this->userRepository->findById($userId);
        }

        // Si l'utisateur existe, mais qu'il ne s'est jamais connecté avec Google
        if (is_null($user->google_id)) {
            // Ajouter son id d'utilisateur Google
            $this->userRepository->addGoogleToUser($user->email, $googleResult['sub']);
        }

        // Créer un token d'accès à l'API
        $token = $user->createToken('google')->accessToken;

        // Retourner le token, et si l'uitilisateur est nouveau dans l'API
        return [
            'token' => $token,
            'is_new' => $isNew
        ];
    }

    public function signUpUser(string $firstName, string $lastName, string $email, string $password): User
    {
        // Trouver l'utilisateur (s'il existe) qui correspond au courriel
        $user = $this->userRepository->findByEmail($email);

        // Générer un code de confirmation à 30 caractères
        $confirmationCode = str_random(30);

        // Si un utilisateur a été trouvé
        if (!is_null($user)) {
            // Ajouter le code de confirmation à l'utilisateur
            $this->userRepository->addConfirmationCode($user->email, $confirmationCode);
        } else {
            // Créer un utilisateur
            $userId = $this->userRepository->createUser(
                $firstName,
                $lastName,
                $email,
                $password,
                $confirmationCode
            );

            // Trouver l'utilisateur créé
            $user = $this->userRepository->findById($userId);
        }

        // Envoyer un courriel de confirmation à l'utilisateur
        Mail::send(new ConfirmAccount(
            $email,
            $confirmationCode,
            $user->first_name
        ));

        // Retourner l'utilisateur créé
        return $user;
    }
}
