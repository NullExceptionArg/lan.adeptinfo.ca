<?php

namespace Tests;

use App\Utils\FacebookUtils;
use Facebook\Exceptions\FacebookSDKException;

/**
 * Contexte pour les tests qui manipulent des utilisateurs de test de l'API de Facebook
 *
 * Class FacebookTestCase
 * @package Tests
 */
abstract class FacebookTestCase extends TestCase
{
    /**
     * Créer l'application
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function tearDown()
    {
        // Obtenir le token d'accès Facebook de l'application
        $accessToken = FacebookUtils::getAccessToken();

        // Créer une connection à l'API de facebook
        $fb = FacebookUtils::getFacebook();
        try {
            // Obtenir les utilisateurs de tests de l'API Facebook
            $users = $fb->get(
                '/' . env('FB_ID') . '/accounts/test-users',
                $accessToken->getValue()
            );

            // Pour chaque utilisateurs de test
            foreach ($users->getDecodedBody()['data'] as $user) {
                // Supprimer l'utilisateur
                $fb->delete(
                    '/' . $user['id'],
                    array(),
                    $accessToken->getValue()
                );
            }
        } catch (FacebookSDKException $e) {
            $this->fail('There was a problem in the FacebookTestCase while contacting the Facebook API.');
        }

        parent::tearDown();
    }
}
