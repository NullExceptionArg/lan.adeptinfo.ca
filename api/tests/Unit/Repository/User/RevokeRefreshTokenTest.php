<?php

namespace Tests\Unit\Repository\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;

class RevokeRefreshTokenTest extends TestCase
{
    use DatabaseMigrations;

    protected $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->app->make('App\Repositories\Implementation\UserRepositoryImpl');
    }

    public function testRevokeRefreshToken(): void
    {
        $this->artisan('passport:install');
        $password = 'Passw0rd!';
        $user = factory('App\Model\User')->create([
            'password' => Hash::make($password),
            'is_confirmed' => true
        ]);
        $clientSecret = DB::table('oauth_clients')
            ->where('id', 2)
            ->first()
            ->secret;

        $this->call('POST', '/api/oauth/token', [
            'grant_type' => 'password',
            'client_id' => 2,
            'client_secret' => $clientSecret,
            'username' => $user->email,
            'password' => $password
        ]);

        $refreshToken = $clientSecret = DB::table('oauth_refresh_tokens')
            ->first()
            ->id;

        $this->seeInDatabase('oauth_refresh_tokens', [
            'id' => $refreshToken,
            'revoked' => false,
        ]);

        $this->userRepository->revokeRefreshToken(Passport::token()->first());

        $this->seeInDatabase('oauth_refresh_tokens', [
            'id' => $refreshToken,
            'revoked' => true,
        ]);
    }
}
