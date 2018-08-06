<?php


namespace App\Repositories\Implementation;


use App\Model\User;
use App\Repositories\UserRepository;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Token;

class UserRepositoryImpl implements UserRepository
{
    public function createUser(string $firstName, string $lastName, string $email, string $password): User
    {
        $user = new User();
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();

        return $user;
    }

    public function deleteUserById(int $userId): void
    {
        User::destroy($userId);
    }

    public function revokeAccessToken(Token $token): void
    {
        $token->revoke();
    }

    public function revokeRefreshToken(Token $token): void
    {
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $token->id)
            ->update([
                'revoked' => true
            ]);
    }

    public function findByEmail(string $userEmail): ?User
    {
        return User::where('email', $userEmail)->first();
    }

    public function findById(int $userId): ?User
    {
        return User::find($userId);
    }

    public function getPaginatedUsersCriteria(
        string $queryString,
        string $orderColumn,
        string $orderDirection,
        int $itemsPerPage,
        int $currentPage
    ): AbstractPaginator
    {
        return User::where('last_name', 'like', '%' . $queryString . '%')
            ->orWhere('first_name', 'like', '%' . $queryString . '%')
            ->orWhere('email', 'like', '%' . $queryString . '%')
            ->orderBy($orderColumn, $orderDirection)
            ->paginate($itemsPerPage, ['*'], '', $currentPage);
    }

    public function createFacebookUser(string $facebookId, string $firstName, string $lastName, string $email): User
    {
        $user = new User();
        $user->facebook_id = $facebookId;
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->email = $email;
        $user->save();

        return $user;
    }

    public function addFacebookToUser(User $user, string $facebookId): User
    {
        $user->facebook_id = $facebookId;
        $user->save();

        return $user;
    }
}