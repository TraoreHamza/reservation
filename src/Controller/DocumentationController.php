<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur pour la documentation technique
 * 
 * AMÉLIORATIONS APPORTÉES (Yasmina + Assistant) :
 * - Affichage des diagrammes UML
 * - Documentation technique accessible
 * - Diagrammes de classes et de séquences
 */
class DocumentationController extends AbstractController
{
    #[Route('/documentation', name: 'documentation')]
    public function index(): Response
    {
        return $this->render('documentation/index.html.twig');
    }

    #[Route('/admin/documentation', name: 'admin_documentation')]
    public function admin(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('documentation/admin.html.twig');
    }
}
