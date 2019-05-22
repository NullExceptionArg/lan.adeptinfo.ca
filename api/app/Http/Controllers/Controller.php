<?php

namespace App\Http\Controllers;

use App\Model\Lan;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Contrôleur de base.
 * Contient les méthodes récurentes.
 *
 * Class Controller
 */
class Controller extends BaseController
{
    /**
     * Si aucun LAN n'est spécifié dans une requête qui nécessite un LAN, ce sera le LAN par défaut qui sera utilisé.
     *
     * @param Request $request
     *
     * @return Request
     */
    public function adjustRequestForLan(Request $request): Request
    {
        // Si aucun id de LAN n'est spécifié dans la requête
        if (is_null($request->input('lan_id'))) {
            // Trouver le LAN courant
            $lan = Lan::getCurrent();
            // Ajouter l'id du LAN à la requête.
            // S'il n'y a pas de LAN courant, retourner la requête. La validation devrait retourner une erreur.
            if (!is_null($lan)) {
                $request['lan_id'] = $lan->id;
            }
        }

        return $request;
    }

    /**
     * Si aucune adresse courriel n'est spécifié, utiliser l'adresse courriel de l'utilisateur qui fait la requête.
     * Cette méthode ne devrait être utilisé que pour des requêtes où l'utilisateur est authentifié.
     *
     * @param Request $request
     *
     * @return Request
     */
    public function adjustRequestForEmail(Request $request): Request
    {
        // Si aucune adresse courriel n'est spécifiée dans la requête
        if (is_null($request->input('email'))) {
            // Ajouter l'adresse courriel de l'utilisateur connecté
            $request['email'] = Auth::user()->email;
        }

        return $request;
    }

    /**
     * Lancer une exception de requête invalide si les conditions d'un validateur ne sont pas respectées.
     *
     * @param Validator $validator
     */
    public function checkValidation(Validator $validator)
    {
        if ($validator->fails()) {
            throw new BadRequestHttpException($validator->errors());
        }
    }
}
