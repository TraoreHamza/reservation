<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/room')]
class RoomController extends AbstractController
{
    public function __construct(private RoomRepository $roomRepo) {}

    #[Route('s', name: 'rooms', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $rooms = $this->roomRepo->findAll();
        return $this->render('room/index.html.twig', ['rooms' => $rooms]);
    }

    #[Route('/{id}', name: 'room_view', methods: ['GET'])]
    public function view(int $id): Response
    {
        $room = $this->roomRepo->find($id);
        if (!$room) {
            $this->addFlash('error', 'Salle non trouvée');
            return $this->redirectToRoute('room_index');
        }

        return $this->render('room/view.html.twig', ['room' => $room]);
    }
}
