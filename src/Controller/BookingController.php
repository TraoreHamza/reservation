<?php

namespace App\Controller;


use App\Entity\Booking;
use App\Form\BookingForm;
use App\Service\QuotationService;
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
        private QuotationService $qs
    ) {}

    // Route pour touts les booking (réservations)
    #[Route('s', name: 'bookings', methods: ['GET'])]
    public function index(): Response
    {
        $bookings = $this->br->findBy(['client' => $this->getUser()]);
        return $this->render('booking/index.html.twig', ['bookings' => $bookings]);
    }

    #[Route('/new', name: 'booking_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $data = $request->request->all();
        $booking = new Booking();

        /** @var User $user */
        $user = $this->getUser();

        if (isset($data['room_id'])) {
            $room = $this->rr->find($data['room_id']);
            if (!$room) {
                $this->addFlash('error', 'salle non trouvée');
                return $this->redirectToRoute('room_view', ['id' => $data['room_id']]);
            }
            $booking->setRoom($room);
        }

        if (isset($data['startDate']) && isset($data['endDate'])) {
            $startDate = new \DateTimeImmutable($data['startDate']);
            $endDate = new \DateTimeImmutable($data['endDate']);

            if ($startDate >= $endDate) {
                $this->addFlash('error', 'La date de début doit être antérieure à la date de fin');
                return $this->redirectToRoute('room_view', ['id' => $data['room_id']]);
            }
            if ($startDate < new \DateTimeImmutable()) {
                $this->addFlash('error', 'La date de début ne peut pas être dans le passé');
                return $this->redirectToRoute('room_view', ['id' => $data['room_id']]);
            }

            $booking->setStartDate($startDate);
            $booking->setEndDate($endDate);
        }

        if (isset($data['options'])) {
            $options = explode(',', $data['options']); // Convertir la chaîne en tableau
            foreach ($options as $item) {
                $option = $this->or->find($item);
                if ($option) {
                    $booking->addOption($option);
                }
            }
        }

        if (isset($data['equipments'])) {
            $equipments = explode(',', $data['equipments']); // Convertir la chaîne en tableau
            foreach ($equipments as $item) {
                $equipment = $this->er->find($item);
                if ($equipment) {
                    $booking->addEquipment($equipment);
                }
            }
        }

        $booking->setClient($user->getClient());

        $this->em->persist($booking);
        $this->em->flush();
        $this->qs->create($booking);



        $this->addFlash('success', 'Votre demande de réservation a bien été prise en compte.');
        return $this->redirectToRoute('room_view', ['id' => $data['room_id']]);
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

    #[Route('/{id}/cancel', name: 'booking_cancel', methods: ['GET'])]
    public function cancel(Booking $booking): Response
    {
        $this->em->remove($booking);
        $this->em->flush();
        $this->addFlash('success', 'Réservation annulée');
        return $this->redirectToRoute('bookings');
    }
}
