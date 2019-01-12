<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Changer la langue des réponses
 *
 * Class Language
 * @package App\Http\Middleware
 */
class Language
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
        // Si le paramètre "lang" est spécifié dans la requête, utiliser la langue spécifiée.
        // Sinon utiliser la langue par défaut spécifiée dans .env
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
