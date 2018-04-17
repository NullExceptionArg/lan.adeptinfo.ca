<?php


namespace App\Services;


use App\Model\User;
use Illuminate\Http\Request;

interface UserService
{
    public function signUp(Request $request): User;
}