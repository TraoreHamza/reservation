<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchRoomController extends AbstractController
{
    #[Route('/search-room', name: 'search_room', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $query = $request->query->get('q', '');

        return $this->render('search_room/index.html.twig', [
            'query' => $query
        ]);
    }

    #[Route('/test-search', name: 'test_search', methods: ['GET'])]
    public function test(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findAll();

        return $this->render('search_room/test.html.twig', [
            'rooms' => $rooms
        ]);
    }

    #[Route('/api/search-room', name: 'api_search_room', methods: ['GET'])]
    public function search(Request $request, RoomRepository $roomRepository): JsonResponse
    {
        $query = $request->query->get('q');

        if (!$query) {
            return new JsonResponse([]);
        }

        $results = $roomRepository->searchRooms($query);

        return new JsonResponse($results);
    }
}
