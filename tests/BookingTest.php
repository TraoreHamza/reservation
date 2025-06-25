<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingTest extends WebTestCase
{
    public function testUserCanBookModifyAndCancel(): void
    {
        $client = static::createClient();
        // Connexion utilisateur
        $client->request('GET', '/login');
        $client->submitForm('Connexion', [
            'email' => 'testuser@example.com',
            'password' => 'Test1234!'
        ]);
        $this->assertResponseRedirects();
        $client->followRedirect();
        // Réserver une salle
        $crawler = $client->request('GET', '/rooms');
        $link = $crawler->selectLink('Réserver')->link();
        $crawler = $client->click($link);
        $form = $crawler->selectButton('Réserver')->form([
            'booking_form[startDate]' => (new \DateTime('+2 days'))->format('Y-m-d H:i'),
            'booking_form[endDate]' => (new \DateTime('+2 days +1 hour'))->format('Y-m-d H:i'),
            // TODO: Ajouter les autres champs requis (room, options, équipements...)
        ]);
        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');
        $this->assertSelectorTextContains('body', 'en attente');

        // Modifier la réservation (exemple : changer la date)
        $crawler = $client->request('GET', '/bookings');
        $link = $crawler->selectLink('Modifier')->link();
        $crawler = $client->click($link);
        $form = $crawler->selectButton('Modifier')->form([
            'booking_form[endDate]' => (new \DateTime('+2 days +2 hours'))->format('Y-m-d H:i'),
        ]);
        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');

        // Annuler la réservation
        $crawler = $client->request('GET', '/bookings');
        $link = $crawler->selectLink('Annuler')->link();
        $client->click($link);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'Aucune réservation');
    }
}
