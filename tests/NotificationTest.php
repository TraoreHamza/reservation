<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationTest extends WebTestCase
{
    public function testAdminReceivesPendingBookingNotification(): void
    {
        $client = static::createClient();
        // Connexion admin
        $client->request('GET', '/login');
        $client->submitForm('Connexion', [
            'email' => 'admin@example.com',
            'password' => 'Admin1234!'
        ]);
        $this->assertResponseRedirects();
        $client->followRedirect();
        // Accès dashboard
        $crawler = $client->request('GET', '/admin');
        // Vérifier la présence d'une notification pour pré-réservations en attente
        $this->assertSelectorTextContains('body', 'pré-réservations en attente');
        // Vérifier la couleur ou l'icône d'alerte
        $this->assertSelectorExists('.alert-danger, .badge-warning, .text-danger');
    }
}
