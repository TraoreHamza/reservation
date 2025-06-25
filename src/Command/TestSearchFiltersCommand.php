<?php

namespace App\Command;

use App\Repository\RoomRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-search-filters',
    description: 'Test des filtres de recherche de salles',
)]
class TestSearchFiltersCommand extends Command
{
    public function __construct(
        private RoomRepository $roomRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Test des filtres de recherche de salles');

        // Test 1: Recherche par terme
        $io->section('Test 1: Recherche par terme "Victor"');
        $results = $this->roomRepository->searchRooms('Victor');
        $io->text(sprintf('Résultats trouvés: %d', count($results)));
        foreach ($results as $room) {
            $io->text(sprintf('- %s (Capacité: %d, Prix: %.2f€)', $room->getName(), $room->getCapacity(), $room->getPrice()));
        }

        // Test 2: Filtrage par capacité
        $io->section('Test 2: Filtrage par capacité (min: 10, max: 50)');
        $results = $this->roomRepository->searchRooms(null, null, null, null, null, null, 10, 50);
        $io->text(sprintf('Résultats trouvés: %d', count($results)));
        foreach ($results as $room) {
            $io->text(sprintf('- %s (Capacité: %d)', $room->getName(), $room->getCapacity()));
        }

        // Test 3: Filtrage par équipement
        $io->section('Test 3: Filtrage par équipement "Vidéoprojecteur"');
        $results = $this->roomRepository->searchRooms(null, null, 'Vidéoprojecteur');
        $io->text(sprintf('Résultats trouvés: %d', count($results)));
        foreach ($results as $room) {
            $io->text(sprintf('- %s', $room->getName()));
        }

        // Test 4: Filtrage par option
        $io->section('Test 4: Filtrage par option "Wi-Fi"');
        $results = $this->roomRepository->searchRooms(null, 'Wi-Fi');
        $io->text(sprintf('Résultats trouvés: %d', count($results)));
        foreach ($results as $room) {
            $io->text(sprintf('- %s', $room->getName()));
        }

        // Test 5: Filtrage par luminosité
        $io->section('Test 5: Filtrage par luminosité naturelle');
        $results = $this->roomRepository->searchRooms(null, null, null, null, true);
        $io->text(sprintf('Résultats trouvés: %d', count($results)));
        foreach ($results as $room) {
            $io->text(sprintf('- %s (Luminosité: %s)', $room->getName(), $room->hasLuminosity() ? 'Oui' : 'Non'));
        }

        // Test 6: Filtrage par accessibilité PMR
        $io->section('Test 6: Filtrage par accessibilité PMR');
        $results = $this->roomRepository->searchRooms(null, null, null, null, null, true);
        $io->text(sprintf('Résultats trouvés: %d', count($results)));
        foreach ($results as $room) {
            $io->text(sprintf('- %s (PMR: %s)', $room->getName(), $room->hasPmrAccess() ? 'Oui' : 'Non'));
        }

        // Test 7: Combinaison de filtres
        $io->section('Test 7: Combinaison de filtres (Capacité 10-100 + Luminosité)');
        $results = $this->roomRepository->searchRooms(null, null, null, null, true, null, 10, 100);
        $io->text(sprintf('Résultats trouvés: %d', count($results)));
        foreach ($results as $room) {
            $io->text(sprintf(
                '- %s (Capacité: %d, Luminosité: %s)',
                $room->getName(),
                $room->getCapacity(),
                $room->hasLuminosity() ? 'Oui' : 'Non'
            ));
        }

        $io->success('Tous les tests de filtrage ont été effectués avec succès !');

        return Command::SUCCESS;
    }
}
