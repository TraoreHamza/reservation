<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingForm;
use App\Form\BookingFormType;
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
        private OptionRepository $or
    ) {}

    #[Route('s', name: 'booking_index', methods: ['GET'])]
    public function index(): Response
    {
        $bookings = $this->br->findBy(['user' => $this->getUser()]);
        return $this->render('booking/index.html.twig', ['bookings' => $bookings]);
    }

    #[Route('/book/{id}', name: 'booking_book_room', methods: ['POST'])]
    public function bookRoom(Request $request, int $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $booking = new Booking();
        $data = $request->request->all();
        $booking
            ->setRoom($this->rr->find($id))
            ->setStatus("en attente")
            ->setStartDate($data['startDate'])
            ->setEndDate($data['endDate'])
            ->setClient($user->getClient())
        ;



        if ($data['equipment']) {
            foreach ($data['equipment'] as $value) {
                $booking->addEquipment($this->er->find($value));
            }
        }
        if ($data['options']) {
            foreach ($data['options'] as $value) {
                $booking->addOption($this->or->find($value));
            }
        }


        $this->em->persist($booking);
        $this->em->flush();
        $this->addFlash('success', 'Réservation enregistrée');


        return $this->redirectToRoute('booking_index');
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
            return $this->redirectToRoute('booking_index');
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
        return $this->redirectToRoute('booking_index');
    }
}
