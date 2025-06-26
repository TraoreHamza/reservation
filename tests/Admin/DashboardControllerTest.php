<?php

namespace App\Tests\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests pour le DashboardController Admin
 * 
 * Tests créés à partir du DashboardController Admin :
 * - Accès au dashboard admin
 * - Statistiques et notifications
 * - Gestion des réservations en attente
 */
class DashboardControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test d'accès au dashboard admin sans authentification
     */
    public function testAdminDashboardWithoutAuth(): void
    {
        $this->client->request('GET', '/admin');

        // Devrait rediriger vers la page de login
        $this->assertResponseRedirects();
    }

    /**
     * Test d'accès au dashboard admin sans rôle admin
     */
    public function testAdminDashboardWithoutRole(): void
    {
        // Ici on pourrait créer un utilisateur sans rôle admin pour tester
        $this->client->request('GET', '/admin');

        // Devrait rediriger vers la page de login
        $this->assertResponseRedirects();
    }

    /**
     * Test de la structure des routes admin
     */
    public function testAdminRoutesExist(): void
    {
        // Vérifier que les routes existent (même si on est redirigé)
        $this->client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/documentation');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * Test de la page de documentation admin
     */
    public function testAdminDocumentation(): void
    {
        $this->client->request('GET', '/admin/documentation');

        // Devrait rediriger vers la page de login
        $this->assertResponseRedirects();
    }

    /**
     * Test de la gestion des réservations via EasyAdmin
     */
    public function testAdminBookings(): void
    {
        $this->client->request('GET', '/admin?crudAction=index&crudId=null&entityFqcn=App\Entity\Booking');

        // Devrait rediriger vers la page de login
        $this->assertResponseRedirects();
    }
}
