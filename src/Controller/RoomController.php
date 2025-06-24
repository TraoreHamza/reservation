<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\User;
use App\Form\BookingForm;
use App\Repository\RoomRepository;
use App\Service\QuotationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/room')]
class RoomController extends AbstractController
{
    public function __construct(
        private RoomRepository $roomRepo,
        private EntityManagerInterface $em
    ) {}

    // Route pour toutes les rooms (salles)
    #[Route('s', name: 'rooms', methods: ['GET'])]
    public function index(): Response
    {
        $rooms = $this->roomRepo->findAll();
        return $this->render('room/index.html.twig', ['rooms' => $rooms]);
    }

    // Route pour une room (salle)
    #[Route('/{id}', name: 'room_view', methods: ['GET', 'POST'])]
    public function view(int $id, Request $request, QuotationService $qs): Response
    {

        $room = $this->roomRepo->find($id);
        if (!$room) {
            $this->addFlash('danger', 'Salle non trouvée');
            return $this->redirectToRoute('rooms');
        }

        $booking = new Booking;
        /** @var User $user */
        $user = $this->getUser();


        $form = $this->createForm(BookingForm::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setRoom($room)->setClient($user->getClient());

            $this->em->persist($booking);
            $this->em->flush();

            // Créer le devis avec l'utilisateur connecté comme créateur
            $qs->createFromBooking($booking, $user);

            $this->addFlash('success', 'Votre demande de réservation a bien été enregistrée');
            return $this->redirectToRoute('bookings');
        }

        return $this->render('room/view.html.twig', [
            'room' => $room,
            'bookingForm' => $form
        ]);
    }
}
