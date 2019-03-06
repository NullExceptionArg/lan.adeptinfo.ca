<?php

namespace App\Http\Middleware;

use App\Model\User;
use Closure;

/**
 * S'assurer que l'utilisateur qui accède à l'application est confirmé (par courriel)
 *
 * Class Login
 * @package App\Http\Middleware
 */
class Login
{
    /**
     * Traiter une demande entrante.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = User::where('email', $request->input('username'))->first();
        if (is_null($user) || !$user->is_confirmed) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
