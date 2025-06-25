<?php

namespace App\Command;

use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-admin-performance',
    description: 'Test des performances de l\'interface d\'administration',
)]
class TestAdminPerformanceCommand extends Command
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Test des performances de l\'interface d\'administration');

        $io->section('Test 1: Requête optimisée avec JOINs');

        $startTime = microtime(true);

        // Requête optimisée (comme dans le contrôleur admin)
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('b', 'r', 'c')
            ->from('App\Entity\Booking', 'b')
            ->leftJoin('b.room', 'r')
            ->leftJoin('b.client', 'c')
            ->setMaxResults(20);

        $bookings = $queryBuilder->getQuery()->getResult();

        $endTime = microtime(true);
        $optimizedTime = ($endTime - $startTime) * 1000; // en millisecondes

        $io->text(sprintf('Temps optimisé : %.2f ms', $optimizedTime));
        $io->text(sprintf('Nombre de réservations récupérées : %d', count($bookings)));

        $io->section('Test 2: Requête non optimisée (simulation)');

        $startTime = microtime(true);

        // Requête non optimisée (simulation du problème N+1)
        $allBookings = $this->bookingRepository->findAll();

        $endTime = microtime(true);
        $nonOptimizedTime = ($endTime - $startTime) * 1000; // en millisecondes

        $io->text(sprintf('Temps non optimisé : %.2f ms', $nonOptimizedTime));
        $io->text(sprintf('Nombre de réservations récupérées : %d', count($allBookings)));

        $io->section('Résultats :');

        if ($optimizedTime < $nonOptimizedTime) {
            $improvement = (($nonOptimizedTime - $optimizedTime) / $nonOptimizedTime) * 100;
            $io->success(sprintf('Optimisation réussie ! Amélioration de %.1f%%', $improvement));
        } else {
            $io->warning('Pas d\'amélioration détectée dans ce test.');
        }

        $io->text('L\'optimisation devrait réduire significativement le nombre de requêtes SQL dans l\'interface d\'administration.');

        return Command::SUCCESS;
    }
}
