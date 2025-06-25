<?php

namespace App\Command;

use App\Entity\Booking;
use App\Message\BookingReminderMessage;
use App\Repository\BookingRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:test-messenger-reminders',
    description: 'Teste le système de rappels avec Messenger',
)]
class TestMessengerRemindersCommand extends Command
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private MessageBusInterface $messageBus
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Test du système Messenger de rappels');

        // Récupérer une réservation validée
        $booking = $this->bookingRepository->findOneBy(['status' => 'validated']);

        if (!$booking) {
            $io->error('Aucune réservation validée trouvée. Veuillez d\'abord valider une réservation.');
            return Command::FAILURE;
        }

        $io->info(sprintf(
            'Test de Messenger pour la réservation %d (Client: %s, Salle: %s)',
            $booking->getId(),
            $booking->getClient()?->getName(),
            $booking->getRoom()?->getName()
        ));

        // Test des différents types de rappels
        $reminders = [
            7200 => '5 jours avant',
            60 => '1 heure avant',
            30 => '30 minutes avant',
            10 => '10 minutes avant',
            2 => '2 minutes avant'
        ];

        foreach ($reminders as $minutes => $description) {
            try {
                $io->text("Envoi du message Messenger : {$description}...");

                // Créer et dispatcher le message
                $message = new BookingReminderMessage($booking->getId(), $minutes);
                $this->messageBus->dispatch($message);

                $io->text("✅ {$description} : message envoyé dans la file d'attente");
            } catch (\Exception $e) {
                $io->text("❌ {$description} : erreur - " . $e->getMessage());
            }
        }

        $io->success('Test des messages Messenger terminé !');
        $io->info('Pour traiter les messages, lancez : php bin/console messenger:consume async');

        return Command::SUCCESS;
    }
}
