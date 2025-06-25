<?php

namespace App\Command;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-booking',
    description: 'Test de création d\'une réservation pour vérifier les erreurs',
)]
class TestBookingCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            // Récupérer une salle
            $room = $this->entityManager->getRepository(Room::class)->findOneBy([]);
            if (!$room) {
                $io->error('Aucune salle trouvée');
                return Command::FAILURE;
            }

            // Récupérer un client
            $client = $this->entityManager->getRepository(Client::class)->findOneBy([]);
            if (!$client) {
                $io->error('Aucun client trouvé');
                return Command::FAILURE;
            }

            // Créer une réservation de test
            $booking = new Booking();
            $booking->setRoom($room);
            $booking->setClient($client);
            $booking->setStartDate(new \DateTimeImmutable('+1 day'));
            $booking->setEndDate(new \DateTimeImmutable('+2 days'));
            $booking->setStatus('pending');

            $this->entityManager->persist($booking);
            $this->entityManager->flush();

            $io->success(sprintf(
                'Réservation créée avec succès ! ID: %d, Salle: %s, Client: %s',
                $booking->getId(),
                $room->getName(),
                $client->getName()
            ));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur lors de la création de la réservation: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
