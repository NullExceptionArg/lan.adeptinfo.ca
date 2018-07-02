<?php


namespace App\Services;


use App\Http\Resources\User\GetUserCollection;
use App\Model\User;
use Illuminate\Http\Request;

interface UserService
{
    public function signUpUser(Request $request): User;

    public function deleteUser(): void;

    public function logOut(): void;

    public function getUsers(Request $request): GetUserCollection;
}