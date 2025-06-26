<?php

namespace App\Controller;


use App\Entity\Booking;
use App\Form\BookingForm;
use App\Repository\RoomRepository;
use App\Repository\OptionRepository;
use App\Repository\BookingRepository;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/booking')]
class BookingController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private BookingRepository $br,
        private RoomRepository $rr,
        private EquipmentRepository $er,
        private OptionRepository $or,
    ) {}

    // Route pour touts les booking (réservations)
    #[Route('/s', name: 'bookings', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $client = $user->getClient();

        if (!$client) {
            $this->addFlash('warning', 'Aucun client associé à votre compte.');
            return $this->render('booking/index.html.twig', ['bookings' => []]);
        }

        $bookings = $this->br->findBy(['client' => $client]);
        return $this->render('booking/index.html.twig', ['bookings' => $bookings]);
    }

    #[Route('/new', name: 'booking_new')]
    public function new(Request $request): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingForm::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier que la salle est sélectionnée
            if (!$booking->getRoom()) {
                $this->addFlash('error', 'Veuillez sélectionner une salle.');
                return $this->render('booking/new.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Récupérer le client associé à l'utilisateur connecté
            $user = $this->getUser();
            
            if (!$user) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour créer une réservation.');
            }
            
            $client = $user->getClient();

            if (!$client) {
                $this->addFlash('error', 'Aucun client associé à votre compte.');
                return $this->redirectToRoute('bookings');
            }

            $booking->setClient($client);
            $this->em->persist($booking);
            $this->em->flush();
            $this->addFlash('success', 'Réservation créée !');
            return $this->redirectToRoute('bookings');
        }

        return $this->render('booking/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route pour afficher une réservation individuelle
    #[Route('/{id}', name: 'booking_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Booking $booking): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir cette réservation.');
        }

        $client = $user->getClient();

        // Vérifier que l'utilisateur peut voir cette réservation
        if (!$client || $booking->getClient() !== $client) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette réservation.');
        }

        return $this->render('booking/show.html.twig', [
            'booking' => $booking
        ]);
    }

    #[Route('/{id}/edit', name: 'booking_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Booking $booking): Response
    {
        $form = $this->createForm(BookingForm::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($booking);
            $this->em->flush();
            $this->addFlash('success', 'Réservation mise à jour');
            return $this->redirectToRoute('bookings');
        }

        return $this->render('booking/edit.html.twig', [
            'form' => $form,
            'booking' => $booking
        ]);
    }

    #[Route('/{id}/cancel', name: 'booking_cancel', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function cancel(Booking $booking): Response
    {
        $this->em->remove($booking);
        $this->em->flush();
        $this->addFlash('success', 'Réservation annulée');
        return $this->redirectToRoute('bookings');
    }
}
