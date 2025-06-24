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
    #[Route('s', name: 'bookings', methods: ['GET'])]
    public function index(): Response
    {
        $bookings = $this->br->findBy(['user' => $this->getUser()]);
        return $this->render('booking/index.html.twig', ['bookings' => $bookings]);
    }


    #[Route('/{id}/edit', name: 'booking_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id}/cancel', name: 'booking_cancel', methods: ['POST'])]
    public function cancel(Booking $booking): Response
    {
        $this->em->remove($booking);
        $this->em->flush();
        $this->addFlash('success', 'Réservation annulée');
        return $this->redirectToRoute('bookings');
    }
}
