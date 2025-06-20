<?php

namespace App\EventListener;

use App\Entity\Booking;
use App\Event\BookingCreatedEvent;
use App\Event\BookingUpdatedEvent;
use App\Event\BookingCancelledEvent;
use App\Event\BookingValidatedEvent;
use App\Event\BookingReminderEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationListener
{
    public function __construct(private MailerInterface $mailer) {}

    public function onBookingCreated(BookingCreatedEvent $event): void
    {
        $booking = $event->getBooking();
        $client = $booking->getClient();
        $adminEmail = 'admin@monsite.com';

        // Notification pour l'utilisateur
        $this->send(
            $client->getUser()?->getEmail(),
            'Réservation enregistrée',
            "Votre réservation pour la salle " . $booking->getRoom()->getName() . " a bien été prise en compte."
        );

        // Notification pour l'admin
        $this->send(
            $adminEmail,
            'Nouvelle réservation',
            "Nouvelle réservation effectuée par " . $client->getUser()?->getEmail()
        );
    }

    public function onBookingUpdated(BookingUpdatedEvent $event): void
    {
        $booking = $event->getBooking();
        $client = $booking->getClient();
        $adminEmail = 'admin@monsite.com';

        $this->send(
            $client->getUser()?->getEmail(),
            'Réservation modifiée',
            "Votre réservation pour la salle " . $booking->getRoom()->getName() . " a été modifiée."
        );

        $this->send(
            $adminEmail,
            'Réservation modifiée',
            "La réservation de " . $client->getUser()?->getEmail() . " a été modifiée."
        );
    }

    public function onBookingCancelled(BookingCancelledEvent $event): void
    {
        $booking = $event->getBooking();
        $client = $booking->getClient();
        $adminEmail = 'admin@monsite.com';

        $this->send(
            $client->getUser()?->getEmail(),
            'Réservation annulée',
            "Votre réservation a bien été annulée."
        );

        $this->send(
            $adminEmail,
            'Réservation annulée',
            "La réservation de " . $client->getUser()?->getEmail() . " a été annulée."
        );
    }

    public function onBookingValidated(BookingValidatedEvent $event): void
    {
        $booking = $event->getBooking();
        $client = $booking->getClient();

        $this->send(
            $client->getUser()?->getEmail(),
            'Réservation validée',
            "Votre réservation pour la salle " . $booking->getRoom()->getName() . " a été validée par l'administrateur."
        );
    }

    public function onBookingReminder(BookingReminderEvent $event): void
    {
        $booking = $event->getBooking();
        $adminEmail = 'admin@monsite.com';

        $this->send(
            $adminEmail,
            'Rappel - Réservation dans 5 jours',
            "La réservation pour la salle " . $booking->getRoom()->getName() . " arrive bientôt."
        );
    }

    private function send(string $to, string $subject, string $body): void
    {
        $email = (new Email())
            ->from('no-reply@monsite.com')
            ->to($to)
            ->subject($subject)
            ->text($body)
            ->html($body);

        $this->mailer->send($email);
    }
}
