<?php

namespace Tests;

use App\Utils\FacebookUtils;
use Facebook\Exceptions\FacebookSDKException;

abstract class FacebookTestCase extends TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function tearDown()
    {
        $accessToken = FacebookUtils::getAccessToken();
        $fb = FacebookUtils::getFacebook();
        try {
            $users = $fb->get(
                '/' . env('FB_TEST_ID') . '/accounts/test-users',
                $accessToken->getValue()
            );

            foreach ($users->getDecodedBody()['data'] as $user) {
                $fb->delete(
                    '/' . $user['id'],
                    array(),
                    $accessToken->getValue()
                );
            }
        } catch (FacebookSDKException $e) {
        }

        parent::tearDown();
    }
}
