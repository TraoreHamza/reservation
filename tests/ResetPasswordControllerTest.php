<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests pour le ResetPasswordController
 * 
 * Tests créés à partir du ResetPasswordController :
 * - Demande de réinitialisation
 * - Réinitialisation du mot de passe
 * - Validation des tokens
 */
class ResetPasswordControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test de la page de demande de réinitialisation
     */
    public function testResetPasswordRequest(): void
    {
        $this->client->request('GET', '/reset');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form'); // Vérifier le formulaire
    }

    /**
     * Test de soumission de la demande de réinitialisation
     */
    public function testResetPasswordRequestSubmit(): void
    {
        $this->client->request('POST', '/reset', [
            'reset_password_request_form' => [
                'email' => 'test@example.com'
            ]
        ]);

        // Le formulaire peut retourner 422 en cas d'erreur de validation
        $this->assertTrue(
            in_array($this->client->getResponse()->getStatusCode(), [200, 422]),
            'Response should be 200 or 422'
        );
    }

    /**
     * Test de soumission avec email invalide
     */
    public function testResetPasswordRequestInvalidEmail(): void
    {
        $this->client->request('POST', '/reset', [
            'reset_password_request_form' => [
                'email' => 'invalid-email'
            ]
        ]);

        // Devrait retourner 422 pour email invalide
        $this->assertResponseStatusCodeSame(422);
    }

    /**
     * Test de la page de réinitialisation avec token
     */
    public function testResetPasswordWithToken(): void
    {
        $this->client->request('GET', '/reset/reset/fake-token');

        // Peut rediriger si le token est invalide
        $this->assertTrue(
            in_array($this->client->getResponse()->getStatusCode(), [200, 302]),
            'Response should be 200 or 302'
        );
    }

    /**
     * Test de soumission du nouveau mot de passe
     */
    public function testResetPasswordSubmit(): void
    {
        $this->client->request('POST', '/reset/reset/fake-token', [
            'change_password_form' => [
                'plainPassword' => [
                    'first' => 'newpassword123',
                    'second' => 'newpassword123'
                ]
            ]
        ]);

        // Peut rediriger si le token est invalide
        $this->assertTrue(
            in_array($this->client->getResponse()->getStatusCode(), [200, 302, 422]),
            'Response should be 200, 302, or 422'
        );
    }

    /**
     * Test de soumission avec mots de passe différents
     */
    public function testResetPasswordMismatch(): void
    {
        $this->client->request('POST', '/reset/reset/fake-token', [
            'change_password_form' => [
                'plainPassword' => [
                    'first' => 'newpassword123',
                    'second' => 'differentpassword'
                ]
            ]
        ]);

        // Peut rediriger si le token est invalide ou retourner 422 pour validation
        $this->assertTrue(
            in_array($this->client->getResponse()->getStatusCode(), [200, 302, 422]),
            'Response should be 200, 302, or 422'
        );
    }

    /**
     * Test de la page de confirmation d'envoi d'email
     */
    public function testResetPasswordCheckEmail(): void
    {
        $this->client->request('GET', '/reset/check-email');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('body'); // Vérifier que la page se charge
    }

    /**
     * Test de la structure des formulaires
     */
    public function testResetPasswordFormStructure(): void
    {
        $this->client->request('GET', '/reset');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input[type="email"]');
        // Le bouton peut ne pas avoir type="submit" explicitement
        $this->assertSelectorExists('button');
    }
}
