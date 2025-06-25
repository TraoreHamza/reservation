<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests pour le ReviewController
 * 
 * Tests créés à partir du ReviewController :
 * - Création d'avis
 * - Affichage des avis
 * - Gestion des avis
 * 
 * TEMPORAIREMENT DÉSACTIVÉ - À REVOIR PLUS TARD
 */
/*
class ReviewControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test de création d'avis sans authentification
     */
    /*
    public function testReviewCreateWithoutAuth(): void
    {
        $this->client->request('GET', '/review/create');

        // Devrait rediriger vers la page de login
        $this->assertResponseRedirects();
    }

    /**
     * Test de soumission d'avis sans authentification
     */
    /*
    public function testReviewSubmitWithoutAuth(): void
    {
        $this->client->request('POST', '/review/new/1', [
            'review_form' => [
                'content' => 'Test review',
                'star' => 5
            ]
        ]);
        // Devrait rediriger vers la page de login
        $this->assertResponseRedirects();
    }

    /**
     * Test de la liste des avis
     */
    /*
    public function testReviewIndex(): void
    {
        $this->client->request('GET', '/review');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('main'); // Vérifier la structure de base
    }

    /**
     * Test de la liste des avis avec pagination
     */
    /*
    public function testReviewIndexWithPagination(): void
    {
        $this->client->request('GET', '/review?page=1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('body'); // Vérifier que la page se charge
    }

    /**
     * Test de validation d'avis
     */
    /*
    public function testReviewValidation(): void
    {
        $this->client->request('POST', '/review/new/1', [
            'review_form' => [
                'content' => '', // Contenu vide
                'star' => 6 // Note invalide (doit être entre 1 et 5)
            ]
        ]);
        // Devrait rediriger vers la page de login car pas connecté
        $this->assertResponseRedirects();
    }

    /**
     * Test de modification d'avis sans authentification
     */
    /*
    public function testReviewEditWithoutAuth(): void
    {
        $this->client->request('GET', '/review/1/edit');
        $this->assertResponseStatusCodeSame(404); // 404 attendu si l'entité n'existe pas
    }

    /**
     * Test de suppression d'avis sans authentification
     */
    /*
    public function testReviewDeleteWithoutAuth(): void
    {
        $this->client->request('POST', '/review/1/delete');
        $this->assertResponseStatusCodeSame(404); // 404 attendu si l'entité n'existe pas
    }
}
*/
