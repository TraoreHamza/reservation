<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests pour le PageController
 * 
 * Tests créés à partir du PageController :
 * - Page d'accueil
 * - Pages de contenu statique
 */
class PageControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test de la page d'accueil
     */
    public function testHomePage(): void
    {
        $this->client->request('GET', '/');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1'); // Vérifier qu'il y a un titre
        $this->assertSelectorExists('.mb-8'); // Vérifier le composant de recherche
    }

    /**
     * Test de la page d'accueil avec paramètres
     */
    public function testHomePageWithParameters(): void
    {
        $this->client->request('GET', '/?query=test&location=Paris');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('button'); // Vérifier qu'il y a un bouton
    }

    /**
     * Test de la structure de la page d'accueil
     */
    public function testHomePageStructure(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        // Vérifier la structure de base
        $this->assertSelectorExists('html');
        $this->assertSelectorExists('body');
        $this->assertSelectorExists('main');
        $this->assertSelectorExists('nav'); // Navigation
    }
}
