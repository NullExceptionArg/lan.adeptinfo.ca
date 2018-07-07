<?php

namespace App\Http\Middleware;

use Closure;

class Language
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
        switch ($request->input('lang')) {
            case 'fr':
                app('translator')->setLocale('fr');
                break;
            case 'en':
                app('translator')->setLocale('en');
                break;
            default:
                app('translator')->setLocale(env('DEFAULT_LANG'));
        }

        return $next($request);
    }
}
