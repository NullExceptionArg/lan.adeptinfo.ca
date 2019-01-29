<?php

namespace App\Services;

use App\Http\Resources\{User\GetAdminRolesResource,
    User\GetAdminSummaryResource,
    User\GetUserCollection,
    User\GetUserDetailsResource,
    User\GetUserSummaryResource};
use App\Model\{Tag, User};

interface UserService
{
    public function confirm(string $confirmationCode): void;

    public function createTag(string $name): Tag;

    public function deleteUser(): void;

    public function getAdminRoles(string $email, int $lanId): GetAdminRolesResource;

    public function getAdminSummary(int $lanId): GetAdminSummaryResource;

    public function getUserDetails(int $lanId, string $email): GetUserDetailsResource;

    public function getUsers(
        ?string $queryString,
        ?string $orderColumn,
        ?string $orderDirection,
        ?int $itemsPerPage,
        ?int $currentPage
    ): GetUserCollection;

    public function getUserSummary(int $lanId): GetUserSummaryResource;

    public function logOut(): void;

    public function signInFacebook(string $accessToken): array;

    public function signInGoogle(string $accessToken): array;

    public function signUpUser(string $firstName, string $lastName, string $email, string $password): User;
}
