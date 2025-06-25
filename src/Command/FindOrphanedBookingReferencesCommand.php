<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:find-orphaned-booking-references',
    description: 'Liste toutes les entités qui référencent un Booking inexistant (orphelin) dans la base',
)]
class FindOrphanedBookingReferencesCommand extends Command
{
    public function __construct(private Connection $connection)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Recherche des références orphelines vers Booking');

        $tables = [
            'quotation' => 'booking_id',
            // Ajoute ici d'autres tables qui référencent Booking si besoin
        ];

        foreach ($tables as $table => $column) {
            $sql = "SELECT * FROM $table WHERE $column IS NOT NULL AND $column NOT IN (SELECT id FROM booking)";
            $results = $this->connection->fetchAllAssociative($sql);
            if (count($results) > 0) {
                $io->section("Table $table :");
                foreach ($results as $row) {
                    $io->writeln('ID: ' . $row['id'] . ' référence Booking inexistant (booking_id=' . $row[$column] . ')');
                }
            } else {
                $io->success("Aucune référence orpheline dans $table.");
            }
        }

        $io->success('Recherche terminée.');
        return Command::SUCCESS;
    }
}
