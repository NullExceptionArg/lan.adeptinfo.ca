<?php

namespace App\Repositories;

use App\Model\Tag;
use App\Model\User;
use Illuminate\Pagination\AbstractPaginator;
use Laravel\Passport\Token;

interface UserRepository
{
    public function addConfirmationCode(string $email, string $confirmationCode): void;

    public function addFacebookToUser(string $email, string $facebookId): void;

    public function addGoogleToUser(string $email, string $googleId): void;

    public function confirmAccount(string $userId): void;

    public function createFacebookUser(string $facebookId, string $firstName, string $lastName, string $email): int;

    public function createGoogleUser(string $googleId, string $firstName, string $lastName, string $email): int;

    public function createTag(
        int $userId,
        string $name
    ): int;

    /**
     * Create a new user
     * @param string $firstName Users first name
     * @param string $lastName Users last name
     * @param string $email Users email
     * @param string $password Users password
     * @param string $confirmationCode Confirmation code to be sent by email
     * @return int Id of the created user
     */
    public function createUser(
        string $firstName,
        string $lastName,
        string $email, string $password,
        string $confirmationCode): int;

    public function deleteUserById(int $userId): void;

    public function findByConfirmationCode(string $confirmationCode): User;

    public function findByEmail(string $userEmail): ?User;

    public function findById(int $userId): ?User;

    public function findTagById(int $id): ?Tag;

    public function getPaginatedUsersCriteria(
        string $queryString,
        string $orderColumn,
        string $orderDirection,
        int $itemsPerPage,
        int $currentPage
    ): AbstractPaginator;

    public function revokeRefreshToken(Token $token): void;
}