<?php

namespace App\Utils;

use Dingo\Api\Exception\InternalHttpException;
use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Illuminate\Http\Response;

class FacebookUtils
{
    public static function getFacebook(): Facebook
    {
        try {
            return new Facebook([
                'app_id' => env('FB_TEST_ID'),
                'app_secret' => env('FB_TEST_CLIENT_SECRET'),
                'default_graph_version' => 'v3.2'
            ]);
        } catch (FacebookSDKException $e) {
            throw new InternalHttpException(new Response(null, 500));
        }
    }

    public static function getAccessToken(): AccessToken
    {
        $expires = time() + 200;
        return new AccessToken(env('FB_TEST_ID') . '|' . env('FB_TEST_CLIENT_SECRET'), $expires);
    }
}
