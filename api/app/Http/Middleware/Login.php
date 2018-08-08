<?php

namespace App\Http\Middleware;

use App\Model\User;
use Closure;

class Login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = User::where('email', $request->input('username'))->first();
        if (!$user->is_confirmed) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
