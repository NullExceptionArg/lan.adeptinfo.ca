<?php


namespace App\Repositories\Implementation;


use App\Model\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
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

    public function deleteUser(Authenticatable $user): void
    {
        $user->delete();
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
}