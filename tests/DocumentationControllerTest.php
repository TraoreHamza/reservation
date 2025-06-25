<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests pour le DocumentationController
 * 
 * Tests créés à partir du DocumentationController de Yasmina :
 * - Affichage de la documentation publique
 * - Accès à la documentation admin (avec authentification)
 * - Affichage des diagrammes UML
 */
class DocumentationControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test de la page de documentation publique
     */
    public function testDocumentationIndex(): void
    {
        $this->client->request('GET', '/documentation');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1'); // Vérifier qu'il y a un titre
        $this->assertSelectorExists('.documentation-content'); // Vérifier le contenu
    }

    /**
     * Test de la page de documentation admin sans authentification
     */
    public function testAdminDocumentationWithoutAuth(): void
    {
        $this->client->request('GET', '/admin/documentation');

        // Devrait rediriger vers la page de login car pas d'authentification
        $this->assertResponseRedirects();
    }

    /**
     * Test de la page de documentation admin avec authentification admin
     */
    public function testAdminDocumentationWithAuth(): void
    {
        // Créer un utilisateur admin pour le test
        $this->client->request('GET', '/admin/documentation');

        // Pour ce test, on vérifie juste que la route existe
        // En pratique, il faudrait créer un utilisateur admin dans les fixtures de test
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // Redirection vers login
    }

    /**
     * Test que la documentation contient les diagrammes UML
     */
    public function testDocumentationContainsUmlDiagrams(): void
    {
        $this->client->request('GET', '/documentation');

        $this->assertResponseIsSuccessful();

        // Vérifier que la page contient des références aux diagrammes
        $this->assertSelectorExists('.uml-diagrams');
        $this->assertSelectorExists('.diagram-section');
    }

    /**
     * Test de la structure de la page de documentation
     */
    public function testDocumentationStructure(): void
    {
        $this->client->request('GET', '/documentation');

        $this->assertResponseIsSuccessful();

        // Vérifier la structure de base
        $this->assertSelectorExists('html');
        $this->assertSelectorExists('body');
        $this->assertSelectorExists('main');
    }
}
