<?php

namespace App\Command;

use Twig\Environment;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Repository\BookingRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTimeImmutable; // Utilisez DateTimeImmutable pour les dates

#[AsCommand(
    name: 'app:send-unprocessed-booking-alerts',
    description: 'Envoie des alertes pour les réservations non traitées 5 jours avant le début.',
)]
class SendUnprocessedBookingAlertsCommand extends Command
{
    private BookingRepository $bookingRepository;
    private UserRepository $userRepository;
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(BookingRepository $bookingRepository, UserRepository $userRepository, MailerInterface $mailer, Environment $twig)
    {
        parent::__construct();
        $this->bookingRepository = $bookingRepository;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Recherche des réservations non traitées...');

        $fiveDaysFromNow = (new DateTimeImmutable())->modify('+5 days');

        // Récupérer les réservations qui commencent dans 5 jours et qui sont en statut 'pending'
        $unprocessedBookings = $this->bookingRepository->findUnprocessedBookingsStartingBefore($fiveDaysFromNow);

        if (empty($unprocessedBookings)) {
            $output->writeln('Aucune réservation non traitée trouvée.');
            return Command::SUCCESS;
        }

        // Récupérer tous les utilisateurs avec le rôle 'ROLE_ADMIN'
        $admins = $this->userRepository->findByRole('ROLE_ADMIN');

        if (empty($admins)) {
            $output->writeln('Aucun administrateur trouvé pour envoyer les alertes.');
            return Command::FAILURE;
        }

        $adminEmails = array_map(fn($admin) => $admin->getEmail(), $admins);

        foreach ($unprocessedBookings as $booking) {
            $output->writeln(sprintf('Envoi d\'une alerte pour la réservation #%d (Salle: %s, Client: %s) ',
                $booking->getId(),
                $booking->getRoom()->getName(),
                $booking->getClient()->getName()
            ));

            $email = (new Email())
                ->from('no-reply@sallevenue.fr') 
                ->to(...$adminEmails)
                ->subject('Alerte: Réservation non traitée imminente !')
                ->html($this->twig->render('emails/unprocessed_booking_alert.html.twig', [
                    'booking' => $booking,
                ]));

            try {
                $this->mailer->send($email);
                $output->writeln('E-mail envoyé avec succès.');
            } catch (\Exception $e) {
                $output->writeln(sprintf('Erreur lors de l\'envoi de l\'e-mail pour la réservation #%d: %s', $booking->getId(), $e->getMessage()));
            }
        }

        $output->writeln('Processus d\'alerte terminé.');

        return Command::SUCCESS;
    }
}