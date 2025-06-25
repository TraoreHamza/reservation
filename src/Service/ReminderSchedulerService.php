<?php

namespace App\Service;

use App\Entity\Booking;
use App\Message\BookingReminderMessage;
use App\Repository\BookingRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class ReminderSchedulerService
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private MessageBusInterface $messageBus
    ) {}

    /**
     * Planifie tous les rappels pour les réservations validées à venir
     */
    public function scheduleAllReminders(): int
    {
        $upcomingBookings = $this->bookingRepository->findUpcomingValidatedBookings();
        $scheduledCount = 0;

        foreach ($upcomingBookings as $booking) {
            $scheduledCount += $this->scheduleRemindersForBooking($booking);
        }

        return $scheduledCount;
    }

    /**
     * Planifie les rappels pour une réservation spécifique
     */
    public function scheduleRemindersForBooking(Booking $booking): int
    {
        $startDate = $booking->getStartDate();
        $now = new \DateTime();
        $scheduledCount = 0;

        // 1. Rappel à 5 jours avant
        $fiveDaysBefore = (clone $startDate)->modify('-5 days');
        if ($fiveDaysBefore > $now) {
            $this->scheduleReminder($booking->getId(), 7200, $fiveDaysBefore);
            $scheduledCount++;
        }

        // 2. Rappels toutes les 2 minutes dans la dernière heure
        $lastHour = (clone $startDate)->modify('-1 hour');
        if ($lastHour > $now) {
            // Créer des rappels toutes les 2 minutes dans la dernière heure
            for ($minutes = 60; $minutes >= 2; $minutes -= 2) {
                $reminderTime = (clone $startDate)->modify("-{$minutes} minutes");
                if ($reminderTime > $now) {
                    $this->scheduleReminder($booking->getId(), $minutes, $reminderTime);
                    $scheduledCount++;
                }
            }
        }

        return $scheduledCount;
    }

    /**
     * Planifie un rappel spécifique
     */
    private function scheduleReminder(int $bookingId, int $minutesBefore, \DateTimeImmutable $reminderTime): void
    {
        $message = new BookingReminderMessage($bookingId, $minutesBefore);

        // Calcul du délai en millisecondes
        $now = new \DateTimeImmutable();
        $delay = max(0, $reminderTime->getTimestamp() - $now->getTimestamp()) * 1000;

        $envelope = (new Envelope($message))->with(new DelayStamp($delay));
        $this->messageBus->dispatch($envelope);
    }

    /**
     * Nettoie les anciens rappels (optionnel)
     */
    public function cleanupOldReminders(): void
    {
        // Cette méthode pourrait être utilisée pour nettoyer les anciens rappels
        // qui ne sont plus nécessaires
    }
}
