<?php


namespace App\Repositories;


use App\Model\User;
use Illuminate\Support\Collection;
use Laravel\Passport\Token;

interface UserRepository
{
    /**
     * Create a new user
     * @param string $firstName Users first name
     * @param string $lastName Users last name
     * @param string $email Users email
     * @param string $password Users password
     * @return User GetUserResource that was created
     */
    public function createUser(string $firstName, string $lastName, string $email, string $password): User;

    public function deleteUserById(int $userId): void;

    public function revokeAccessToken(Token $token): void;

    public function revokeRefreshToken(Token $token): void;

    public function findByEmail(string $userEmail): ?User;

    public function findById(int $userId): ?User;

    public function getUsersCriteria(string $queryString): Collection;
}