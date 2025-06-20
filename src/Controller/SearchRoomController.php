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
    public function index(): Response
    {
        return $this->render('search_room/index.html.twig');
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
