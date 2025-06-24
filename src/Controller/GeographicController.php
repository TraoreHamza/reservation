<?php

namespace App\Controller;

use App\Service\GeographicService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur pour la gestion géographique
 * 
 * AMÉLIORATIONS APPORTÉES (Yasmina + Assistant) :
 * - API pour la recherche géographique
 * - Autocomplétion des communes, départements, régions
 * - Intégration avec la base des communes françaises
 */
class GeographicController extends AbstractController
{
    public function __construct(private GeographicService $geographicService) {}

    /**
     * API pour l'autocomplétion des communes
     */
    #[Route('/api/geographic/search', name: 'api_geographic_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        $type = $request->query->get('type', 'all'); // all, communes, departments, regions

        if (empty($query) || strlen($query) < 2) {
            return new JsonResponse([]);
        }

        $results = [];

        switch ($type) {
            case 'communes':
                $results = $this->geographicService->searchCommunes($query, 10);
                break;
            case 'departments':
                $departments = $this->geographicService->getDepartments();
                $query = strtolower($query);
                $results = array_filter($departments, function ($dept) use ($query) {
                    return strpos(strtolower($dept['name']), $query) !== false ||
                        strpos(strtolower($dept['code']), $query) !== false;
                });
                $results = array_slice(array_values($results), 0, 10);
                break;
            case 'regions':
                $regions = $this->geographicService->getRegions();
                $query = strtolower($query);
                $results = array_filter($regions, function ($region) use ($query) {
                    return strpos(strtolower($region), $query) !== false;
                });
                $results = array_slice(array_values($results), 0, 10);
                break;
            default:
                $results = $this->geographicService->advancedGeographicSearch($query);
                break;
        }

        return new JsonResponse($results);
    }

    /**
     * API pour récupérer tous les départements
     */
    #[Route('/api/geographic/departments', name: 'api_geographic_departments', methods: ['GET'])]
    public function getDepartments(): JsonResponse
    {
        $departments = $this->geographicService->getDepartments();
        return new JsonResponse($departments);
    }

    /**
     * API pour récupérer toutes les régions
     */
    #[Route('/api/geographic/regions', name: 'api_geographic_regions', methods: ['GET'])]
    public function getRegions(): JsonResponse
    {
        $regions = $this->geographicService->getRegions();
        return new JsonResponse($regions);
    }

    /**
     * API pour récupérer les communes d'un département
     */
    #[Route('/api/geographic/departments/{code}/communes', name: 'api_geographic_department_communes', methods: ['GET'])]
    public function getCommunesByDepartment(string $code): JsonResponse
    {
        $communes = $this->geographicService->getCommunesByDepartment($code);
        return new JsonResponse(array_values($communes));
    }

    /**
     * API pour récupérer les communes d'une région
     */
    #[Route('/api/geographic/regions/{region}/communes', name: 'api_geographic_region_communes', methods: ['GET'])]
    public function getCommunesByRegion(string $region): JsonResponse
    {
        $communes = $this->geographicService->getCommunesByRegion($region);
        return new JsonResponse(array_values($communes));
    }
}
