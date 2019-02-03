<?php

namespace App\Utils;

use Dingo\Api\Exception\InternalHttpException;
use Facebook\{Authentication\AccessToken, Exceptions\FacebookSDKException, Facebook};
use Illuminate\Http\Response;

class FacebookUtils
{
    /**
     * Connection à l'API de Facebook.
     *
     * @return Facebook
     */
    public static function getFacebook(): Facebook
    {
        try {
            return new Facebook([
                'app_id' => env('FB_ID'),
                'app_secret' => env('FB_CLIENT_SECRET'),
                'default_graph_version' => 'v3.2'
            ]);
        } catch (FacebookSDKException $e) {
            throw new InternalHttpException(new Response(null, 500));
        }
    }

    /**
     * Créer un token d'accès Facebook à partir des variables d'environement de Facebook
     *
     * @return AccessToken Token facebook créé
     */
    public static function getAccessToken(): AccessToken
    {
        $expires = time() + 200;
        return new AccessToken(env('FB_ID') . '|' . env('FB_CLIENT_SECRET'), $expires);
    }
}
