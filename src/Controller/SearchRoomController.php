<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use App\Repository\EquipmentRepository;
use App\Repository\OptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôleur pour la gestion de la recherche de chambres
 * 
 * AMÉLIORATIONS APPORTÉES (Lawrence + Assistant) :
 * - Séparation claire entre recherche simple et avancée
 * - API JSON pour les requêtes AJAX
 * - Page dédiée pour la recherche avancée
 * - Support de la recherche multi-critères
 * 
 * ROUTES DISPONIBLES :
 * - /search-room : Page de recherche avancée (interface utilisateur)
 * - /api/search-room : API JSON pour requêtes AJAX
 * - /test-search : Page de test pour afficher toutes les chambres
 */
class SearchRoomController extends AbstractController
{
    /**
     * Page de recherche avancée
     * 
     * AFFICHE :
     * - Interface de recherche avec tous les filtres
     * - Utilise le composant search_room_component
     * - Méthode searchRooms() du RoomRepository
     * 
     * @param Request $request
     * @param EquipmentRepository $equipmentRepository
     * @param OptionRepository $optionRepository
     * @return Response
     */
    #[Route('/search-room', name: 'search_room', methods: ['GET'])]
    public function index(Request $request, EquipmentRepository $equipmentRepository, OptionRepository $optionRepository): Response
    {
        $query = $request->query->get('q', '');
        $equipments = $equipmentRepository->findAll();
        $options = $optionRepository->findAll();
        return $this->render('search_room/index.html.twig', [
            'query' => $query,
            'equipments' => $equipments,
            'options' => $options,
        ]);
    }

    /**
     * Page de test pour afficher toutes les chambres
     * 
     * UTILISATION :
     * - Développement et test
     * - Affichage de toutes les chambres sans filtrage
     * 
     * @param RoomRepository $roomRepository
     * @return Response
     */
    #[Route('/test-search', name: 'test_search', methods: ['GET'])]
    public function test(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findAll();

        return $this->render('search_room/test.html.twig', [
            'rooms' => $rooms
        ]);
    }

    /**
     * API de recherche pour requêtes AJAX
     * 
     * FONCTIONNALITÉS :
     * - Retourne les résultats en JSON
     * - Utilise la méthode searchRooms() (recherche avancée)
     * - Support de l'autocomplétion
     * - Recherche multi-critères complète
     * 
     * PARAMÈTRES :
     * - q : Terme de recherche (requis)
     * 
     * RETOUR :
     * - JSON array des chambres correspondantes
     * - Array vide si aucun terme de recherche
     * 
     * @param Request $request
     * @param RoomRepository $roomRepository
     * @return JsonResponse
     */
    #[Route('/api/search-room', name: 'api_search_room', methods: ['GET'])]
    public function search(Request $request, RoomRepository $roomRepository): JsonResponse
    {
        $query = $request->query->get('q');
        $option = $request->query->get('option');
        $equipment = $request->query->get('equipment');
        $location = $request->query->get('location');
        $luminosity = $request->query->get('luminosity') === 'true';
        $pmrAccess = $request->query->get('pmr_access') === 'true';
        $minCapacity = $request->query->get('min_capacity') ? (int) $request->query->get('min_capacity') : null;
        $maxCapacity = $request->query->get('max_capacity') ? (int) $request->query->get('max_capacity') : null;

        if (!$query && !$option && !$equipment && !$location && $luminosity === false && $pmrAccess === false && $minCapacity === null && $maxCapacity === null) {
            return new JsonResponse([]);
        }

        $results = $roomRepository->searchRooms($query, $option, $equipment, $location, $luminosity, $pmrAccess, $minCapacity, $maxCapacity);

        // Structurer les données pour l'API
        $structuredResults = array_map(function ($room) {
            return [
                'id' => (int) $room->getId(),
                'name' => $room->getName(),
                'description' => $room->getDescription(),
                'capacity' => (int) $room->getCapacity(),
                'price' => (float) $room->getPrice(),
                'luminosity' => $room->hasLuminosity(),
                'pmrAccess' => $room->hasPmrAccess(),
                'location' => $room->getLocation() ? [
                    'city' => $room->getLocation()->getCity(),
                    'department' => $room->getLocation()->getDepartment()
                ] : null
            ];
        }, $results);

        return new JsonResponse($structuredResults);
    }
}
