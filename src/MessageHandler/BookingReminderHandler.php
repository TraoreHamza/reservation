<?php

namespace App\MessageHandler;

use App\Entity\Booking;
use App\Message\BookingReminderMessage;
use App\Repository\BookingRepository;
use App\Service\NotificationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
class BookingReminderHandler
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private NotificationService $notificationService
    ) {}

    public function __invoke(BookingReminderMessage $message): void
    {
        $booking = $this->bookingRepository->find($message->getBookingId());

        if (!$booking) {
            throw new UnrecoverableMessageHandlingException(
                sprintf('Booking with ID %d not found', $message->getBookingId())
            );
        }

        // Vérifier que la réservation est toujours valide
        if ($booking->getStatus() !== 'validated') {
            return; // Ne pas envoyer de notification pour les réservations non validées
        }

        // Envoyer la notification
        $this->notificationService->sendBookingReminder($booking, $message->getMinutesBefore());
    }
}
