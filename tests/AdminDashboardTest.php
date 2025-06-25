<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminDashboardTest extends WebTestCase
{
    public function testAdminDashboardFeatures(): void
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
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Tableau de bord');
        // Validation d'une réservation en attente
        $link = $crawler->selectLink('Valider')->link();
        $client->click($link);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'validée');
        // Annulation d'une réservation
        $crawler = $client->request('GET', '/admin');
        $link = $crawler->selectLink('Annuler')->link();
        $client->click($link);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'annulée');
        // Vérification du code couleur
        $this->assertSelectorExists('.badge-success'); // validée
        $this->assertSelectorExists('.badge-warning'); // en attente
        // Notification admin
        $this->assertSelectorTextContains('body', 'pré-réservations en attente');
    }
}
