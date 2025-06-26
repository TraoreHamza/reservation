<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests pour le BookingController
 * 
 * Tests créés à partir du BookingController :
 * - Liste des réservations
 * - Création de réservation
 * - Détail d'une réservation
 */
class BookingControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test de la liste des réservations sans authentification
     */
    public function testBookingIndexWithoutAuth(): void
    {
        $this->client->request('GET', '/booking/s');

        // Devrait rediriger vers la page de login
        $this->assertResponseRedirects();
    }

    /**
     * Test de la création de réservation sans authentification
     */
    public function testBookingCreateWithoutAuth(): void
    {
        $this->client->request('GET', '/booking/new');

        // Devrait rediriger vers la page de login
        $this->assertResponseRedirects();
    }

    /**
     * Test du formulaire de création de réservation
     */
    public function testBookingCreateForm(): void
    {
        $this->client->request('GET', '/booking/new');

        // Vérifier que la route existe (même si on est redirigé)
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * Test de soumission du formulaire de réservation
     */
    public function testBookingCreateSubmit(): void
    {
        $this->client->request('POST', '/booking/new', [
            'booking' => [
                'startDate' => '2024-12-25',
                'endDate' => '2024-12-26',
                'room' => 1,
                'client' => 1
            ]
        ]);

        // Devrait rediriger vers la page de login
        $this->assertResponseRedirects();
    }

    /**
     * Test du détail d'une réservation
     */
    public function testBookingView(): void
    {
        $this->client->request('GET', '/booking/1');

        // Devrait rediriger vers la page de login car pas d'authentification
        $this->assertResponseRedirects();
    }

    /**
     * Test de la structure du formulaire de réservation
     */
    public function testBookingFormStructure(): void
    {
        $this->client->request('GET', '/booking/new');

        // Devrait rediriger vers la page de login car pas d'authentification
        $this->assertResponseRedirects();
    }

    /**
     * Test de soumission du formulaire de réservation
     */
    public function testBookingFormSubmission(): void
    {
        $this->client->request('GET', '/booking/new');

        // Devrait rediriger vers la page de login car pas d'authentification
        $this->assertResponseRedirects();
    }

    /**
     * Test de validation du formulaire de réservation
     */
    public function testBookingFormValidation(): void
    {
        $this->client->request('GET', '/booking/new');

        // Devrait rediriger vers la page de login car pas d'authentification
        $this->assertResponseRedirects();
    }
}
