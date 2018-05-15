<?php


namespace App\Repositories;


use App\Model\User;
use Illuminate\Contracts\Auth\Authenticatable;

interface UserRepository
{
    /**
     * Create a new user
     * @param string $firstName Users first name
     * @param string $lastName Users last name
     * @param string $email Users email
     * @param string $password Users password
     * @return User User that was created
     */
    public function createUser(string $firstName, string $lastName, string $email, string $password): User;

    public function deleteUser(Authenticatable $user): void;
}