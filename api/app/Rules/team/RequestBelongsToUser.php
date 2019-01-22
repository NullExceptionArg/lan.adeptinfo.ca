<?php

namespace App\Rules\Team;

use App\Model\{Request, Tag};
use Illuminate\{Contracts\Validation\Rule};

/**
 * Une requête appartient à un utilisateur.
 *
 * Class RequestBelongsToUser
 * @package App\Rules\Team
 */
class RequestBelongsToUser implements Rule
{
    protected $userId;

    /**
     * RequestBelongsToUser constructor.
     * @param int $userId Id de l'utilisateur
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }


    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $requestId Id de la requête
     * @return bool
     */
    public function passes($attribute, $requestId): bool
    {
        $request = null;
        $tag = null;

        /*
         * Conditions de garde :
         * Une requête existe pour l'id de requête passée
         * Un tag de joueur existe pour la requête passée
         */
        if (is_null($request = Request::find($requestId)) || is_null($tag = Tag::find($request->tag_id))) {
            return true; // Une autre validation devrait échouer
        }

        // L'id de l'utilisateur du tag de la requête correspond à l'id de l'utilisateur passé
        return $tag->user_id == $this->userId;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.request_belongs_to_user');
    }
}
