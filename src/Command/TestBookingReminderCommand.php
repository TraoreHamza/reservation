<?php

namespace App\Command;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Service\NotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-booking-reminder',
    description: 'Teste le système de notifications de rappel pour les réservations',
)]
class TestBookingReminderCommand extends Command
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private NotificationService $notificationService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Test du système de notifications de rappel');

        // Récupérer une réservation validée
        $booking = $this->bookingRepository->findOneBy(['status' => 'validated']);

        if (!$booking) {
            $io->error('Aucune réservation validée trouvée. Veuillez d\'abord valider une réservation.');
            return Command::FAILURE;
        }

        $io->info(sprintf(
            'Test de notification pour la réservation %d (Client: %s, Salle: %s)',
            $booking->getId(),
            $booking->getClient()?->getName(),
            $booking->getRoom()?->getName()
        ));

        try {
            $this->notificationService->sendBookingReminder($booking, 10);
            $io->success('Notification de rappel envoyée avec succès !');
        } catch (\Exception $e) {
            $io->error('Erreur lors de l\'envoi de la notification : ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
