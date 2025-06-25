<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests pour le UserController
 * 
 * Tests créés à partir du UserController :
 * - Profil utilisateur
 * - Modification du profil
 * - Gestion des fichiers
 */
class UserControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test du profil utilisateur sans authentification
     */
    public function testUserProfileWithoutAuth(): void
    {
        $this->client->request('GET', '/user/profile');

        // Devrait rediriger vers la page de login
        $this->assertResponseRedirects();
    }

    /**
     * Test de la page de modification du profil sans authentification
     */
    public function testUserProfileEditWithoutAuth(): void
    {
        $this->client->request('GET', '/user/profile');

        // Devrait rediriger vers la page de login
        $this->assertResponseRedirects();
    }

    /**
     * Test de soumission de modification du profil
     */
    public function testUserProfileEditSubmit(): void
    {
        $this->client->request('POST', '/user/profile', [
            'user_form' => [
                'email' => 'test@example.com'
            ]
        ]);

        // Devrait rediriger vers la page de login car pas connecté
        $this->assertResponseRedirects();
    }

    /**
     * Test de la page de fichiers sans authentification
     */
    public function testUserFicheWithoutAuth(): void
    {
        $this->client->request('GET', '/user/fiche');

        // Devrait rediriger vers la page de login
        $this->assertResponseRedirects();
    }

    /**
     * Test de la structure des routes utilisateur
     */
    public function testUserRoutesExist(): void
    {
        // Vérifier que les routes existent (même si on est redirigé)
        $this->client->request('GET', '/user/profile');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->request('GET', '/user/fiche');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Test de la route favorite (POST)
        $this->client->request('POST', '/user/favorite/1');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
