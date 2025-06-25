<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoomSearchTest extends WebTestCase
{
    public function testRoomSearchAndFilters(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/search-room');
        $this->assertResponseIsSuccessful();
        // Recherche par capacité
        $client->submitForm('Rechercher', ['capacity' => 10]);
        $this->assertSelectorExists('.room-card');
        // Filtre par équipements
        $client->submitForm('Rechercher', ['equipments' => ['Projecteur', 'Tableau blanc']]);
        $this->assertSelectorExists('.room-card');
        // Filtre par ergonomie
        $client->submitForm('Rechercher', ['luminosity' => 1, 'pmrAccess' => 1]);
        $this->assertSelectorExists('.room-card');
    }
}
