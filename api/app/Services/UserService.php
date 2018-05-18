<?php


namespace App\Services;


use App\Model\User;
use Illuminate\Http\Request;

interface UserService
{
    public function signUp(Request $request): User;

    public function delete(Request $request): void;

    public function logOut(): void;
}