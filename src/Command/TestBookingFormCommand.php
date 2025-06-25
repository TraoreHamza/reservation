<?php

namespace App\Command;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\Client;
use App\Form\BookingForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:test-booking-form',
    description: 'Test de soumission du formulaire de réservation pour identifier les erreurs',
)]
class TestBookingFormCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FormFactoryInterface $formFactory,
        private ValidatorInterface $validator
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

            $io->info(sprintf('Test avec salle: %s (ID: %d)', $room->getName(), $room->getId()));
            $io->info(sprintf('Test avec client: %s (ID: %d)', $client->getName(), $client->getId()));

            // Créer une réservation vide
            $booking = new Booking();

            // Créer le formulaire
            $form = $this->formFactory->create(BookingForm::class, $booking, [
                'csrf_protection' => false
            ]);

            // Simuler les données du formulaire
            $formData = [
                'room' => $room->getId(),
                'startDate' => '2025-06-26',
                'endDate' => '2025-06-27',
                'equipments' => [],
                'options' => [],
            ];

            $io->info('Données du formulaire: ' . json_encode($formData));

            // Soumettre le formulaire
            $form->submit($formData);

            $io->info('Formulaire soumis');

            // Vérifier si le formulaire est valide
            if ($form->isValid()) {
                $io->success('Formulaire valide !');

                // Vérifier les données de la réservation
                $io->info('Salle: ' . ($booking->getRoom() ? $booking->getRoom()->getName() : 'NULL'));
                $io->info('Date début: ' . ($booking->getStartDate() ? $booking->getStartDate()->format('Y-m-d') : 'NULL'));
                $io->info('Date fin: ' . ($booking->getEndDate() ? $booking->getEndDate()->format('Y-m-d') : 'NULL'));

                // Définir le client
                $booking->setClient($client);

                // Persister
                $this->entityManager->persist($booking);
                $this->entityManager->flush();

                $io->success(sprintf(
                    'Réservation créée avec succès ! ID: %d',
                    $booking->getId()
                ));

                return Command::SUCCESS;
            } else {
                $io->error('Formulaire invalide !');
                $errors = $form->getErrors(true);
                foreach ($errors as $error) {
                    $io->error($error->getMessage());
                }
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $io->error('Erreur lors du test: ' . $e->getMessage());
            $io->error('Trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
