<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests pour le contrôleur de recherche de chambres
 * 
 * AMÉLIORATIONS APPORTÉES (Lawrence + Assistant) :
 * - Test de l'API de recherche JSON
 * - Vérification de la réponse et du format
 * - Test avec les données de fixtures
 */
class SearchRoomControllerTest extends WebTestCase
{
    /**
     * Test de l'API de recherche de chambres
     * 
     * VÉRIFIE :
     * - La réponse est réussie (200 OK)
     * - Le contenu est au format JSON
     * - Les données ne sont pas vides
     * - La recherche fonctionne avec le terme "Salle"
     */
    public function testApiRecherche(): void
    {
        // Création du client de test
        $client = static::createClient();

        // Requête GET vers l'API de recherche avec le terme "Salle"
        $client->request('GET', '/api/search-room?q=Salle');

        // Vérification que la réponse est réussie (200 OK)
        $this->assertResponseIsSuccessful();

        // Vérification que le header Content-Type est application/json
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        // Récupération du contenu de la réponse
        $contenuReponse = $client->getResponse()->getContent();

        // Vérification que le contenu est un JSON valide
        $this->assertJson($contenuReponse);

        // Décodage du JSON en tableau PHP
        $donnees = json_decode($contenuReponse, true);

        // Vérification que les données ne sont pas vides
        $this->assertNotEmpty($donnees);
    }

    /**
     * Test de l'API de recherche avec un terme vide
     * 
     * VÉRIFIE :
     * - La réponse est réussie même avec un terme vide
     * - Le contenu est un tableau vide
     */
    public function testApiRechercheTermeVide(): void
    {
        // Création du client de test
        $client = static::createClient();

        // Requête GET vers l'API de recherche sans terme
        $client->request('GET', '/api/search-room');

        // Vérification que la réponse est réussie
        $this->assertResponseIsSuccessful();

        // Vérification du header JSON
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        // Récupération et décodage du contenu
        $contenuReponse = $client->getResponse()->getContent();
        $donnees = json_decode($contenuReponse, true);

        // Vérification que le tableau est vide (comportement attendu)
        $this->assertEmpty($donnees);
    }

    /**
     * Test de la page de recherche avancée
     * 
     * VÉRIFIE :
     * - La page se charge correctement
     * - Le template est rendu
     */
    public function testPageRechercheAvancee(): void
    {
        // Création du client de test
        $client = static::createClient();

        // Requête GET vers la page de recherche avancée
        $client->request('GET', '/search-room');

        // Vérification que la réponse est réussie
        $this->assertResponseIsSuccessful();

        // Vérification que la page contient le terme de recherche
        $this->assertSelectorExists('form');
    }
}
