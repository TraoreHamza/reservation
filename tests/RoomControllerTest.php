<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests pour le RoomController
 * 
 * Tests créés à partir du RoomController :
 * - Liste des salles
 * - Détail d'une salle
 * - Filtrage des salles
 */
class RoomControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test de la liste des salles
     */
    public function testRoomIndex(): void
    {
        $this->client->request('GET', '/rooms');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('main'); // Vérifier la structure de base
    }

    /**
     * Test de la liste des salles avec pagination
     */
    public function testRoomIndexWithPagination(): void
    {
        $this->client->request('GET', '/rooms?page=1');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('body'); // Vérifier que la page se charge
    }

    /**
     * Test du détail d'une salle
     */
    public function testRoomView(): void
    {
        // On suppose qu'il y a au moins une salle avec l'ID 1
        $this->client->request('GET', '/room/1');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('body'); // Vérifier que la page se charge
    }

    /**
     * Test du détail d'une salle inexistante
     */
    public function testRoomViewNotFound(): void
    {
        $this->client->request('GET', '/room/999999');
        
        // L'application redirige vers la liste des salles au lieu de retourner 404
        $this->assertResponseRedirects('/rooms');
    }

    /**
     * Test de la structure de la page de détail
     */
    public function testRoomViewStructure(): void
    {
        $this->client->request('GET', '/room/1');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('body'); // Vérifier que la page se charge
    }
}
