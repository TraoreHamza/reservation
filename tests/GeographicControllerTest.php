<?php

namespace App\Tests;

use App\Service\GeographicService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests pour le GeographicController
 * 
 * Tests créés à partir du GeographicController de Yasmina :
 * - API de recherche géographique
 * - Autocomplétion des communes, départements, régions
 * - Récupération des données géographiques
 */
class GeographicControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test de l'API de recherche géographique
     */
    public function testGeographicSearch(): void
    {
        // Test avec une requête valide
        $this->client->request('GET', '/api/geographic/search?q=Paris&type=communes');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
    }

    /**
     * Test de l'API de recherche avec requête trop courte
     */
    public function testGeographicSearchShortQuery(): void
    {
        $this->client->request('GET', '/api/geographic/search?q=P');

        $this->assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEmpty($responseData);
    }

    /**
     * Test de l'API de recherche avec requête vide
     */
    public function testGeographicSearchEmptyQuery(): void
    {
        $this->client->request('GET', '/api/geographic/search');

        $this->assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEmpty($responseData);
    }

    /**
     * Test de l'API pour récupérer les départements
     */
    public function testGetDepartments(): void
    {
        $this->client->request('GET', '/api/geographic/departments');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertNotEmpty($responseData);

        // Vérifier la structure des données
        if (!empty($responseData)) {
            $firstDept = $responseData[0];
            $this->assertArrayHasKey('code', $firstDept);
            $this->assertArrayHasKey('name', $firstDept);
        }
    }

    /**
     * Test de l'API pour récupérer les régions
     */
    public function testGetRegions(): void
    {
        $this->client->request('GET', '/api/geographic/regions');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertNotEmpty($responseData);
    }

    /**
     * Test de l'API pour récupérer les communes d'un département
     */
    public function testGetCommunesByDepartment(): void
    {
        $this->client->request('GET', '/api/geographic/departments/75/communes');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
    }

    /**
     * Test de l'API pour récupérer les communes d'une région
     */
    public function testGetCommunesByRegion(): void
    {
        $this->client->request('GET', '/api/geographic/regions/Île-de-France/communes');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
    }

    /**
     * Test de recherche par type de données
     */
    public function testSearchByType(): void
    {
        // Test recherche de communes
        $this->client->request('GET', '/api/geographic/search?q=Lyon&type=communes');
        $this->assertResponseIsSuccessful();

        // Test recherche de départements
        $this->client->request('GET', '/api/geographic/search?q=Rhône&type=departments');
        $this->assertResponseIsSuccessful();

        // Test recherche de régions
        $this->client->request('GET', '/api/geographic/search?q=Auvergne&type=regions');
        $this->assertResponseIsSuccessful();
    }
}
