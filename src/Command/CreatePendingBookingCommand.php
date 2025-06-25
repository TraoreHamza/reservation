<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-pending-booking',
    description: 'Crée une réservation à valider dans X minutes',
)]
class CreatePendingBookingCommand extends Command
{
    public function __construct(private Connection $connection)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('minutes', InputArgument::REQUIRED, 'Dans combien de minutes la réservation doit commencer ?')
            ->addOption('client', null, InputArgument::OPTIONAL, 'ID du client', 206)
            ->addOption('room', null, InputArgument::OPTIONAL, 'ID de la salle', 103)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $minutes = (int) $input->getArgument('minutes');
        $clientId = (int) $input->getOption('client');
        $roomId = (int) $input->getOption('room');

        $sql = "INSERT INTO booking (status, start_date, end_date, created_at, client_id, room_id)
                VALUES ('pending', datetime('now', '+$minutes minutes'), datetime('now', '+" . ($minutes + 1) . " minutes'), datetime('now'), $clientId, $roomId)";

        $this->connection->executeStatement($sql);

        $io->success("Réservation créée pour le client $clientId, salle $roomId, à valider dans $minutes minutes.");
        return Command::SUCCESS;
    }
}
