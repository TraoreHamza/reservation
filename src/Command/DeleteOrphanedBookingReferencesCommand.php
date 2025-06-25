<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-orphaned-booking-references',
    description: 'Supprime automatiquement les références orphelines vers des bookings supprimés dans la table quotation',
)]
class DeleteOrphanedBookingReferencesCommand extends Command
{
    public function __construct(private Connection $connection)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Suppression des références orphelines vers Booking');

        $table = 'quotation';
        $column = 'booking_id';
        $sql = "SELECT id FROM $table WHERE $column IS NOT NULL AND $column NOT IN (SELECT id FROM booking)";
        $orphans = $this->connection->fetchFirstColumn($sql);

        if (count($orphans) > 0) {
            $io->section("Suppression dans $table :");
            $deleteSql = "DELETE FROM $table WHERE id IN (" . implode(',', $orphans) . ")";
            $this->connection->executeStatement($deleteSql);
            foreach ($orphans as $id) {
                $io->writeln("Suppression de quotation ID: $id");
            }
            $io->success(count($orphans) . ' référence(s) orpheline(s) supprimée(s).');
        } else {
            $io->success('Aucune référence orpheline à supprimer.');
        }

        return Command::SUCCESS;
    }
} 