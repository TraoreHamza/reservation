<?php

namespace App\EventListener;

use App\Event\BookingCreatedEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationListener
{
    public function __construct(private MailerInterface $mailer) {}

    public function onBookingCreated(BookingCreatedEvent $event): void
    {
        $booking = $event->getBooking();
        $userEmail = $booking->getUser()->getEmail();

        $email = (new Email())
            ->from('no-reply@monsite.com')
            ->to($userEmail)
            ->subject('Réservation enregistrée')
            ->text("Votre réservation pour la salle " . $booking->getRoom()->getName() . " a bien été prise en compte.");

        $this->mailer->send($email);
    }
}
