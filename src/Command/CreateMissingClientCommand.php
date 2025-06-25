<?php

namespace App\Command;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-missing-clients',
    description: 'Crée des clients manquants pour les utilisateurs existants',
)]
class CreateMissingClientCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userRepository = $this->entityManager->getRepository(User::class);
        $usersWithoutClient = $userRepository->createQueryBuilder('u')
            ->leftJoin('u.client', 'c')
            ->where('c.id IS NULL')
            ->getQuery()
            ->getResult();

        if (empty($usersWithoutClient)) {
            $io->success('Tous les utilisateurs ont déjà un client associé.');
            return Command::SUCCESS;
        }

        $io->note(sprintf('Trouvé %d utilisateur(s) sans client associé.', count($usersWithoutClient)));

        foreach ($usersWithoutClient as $user) {
            $client = new Client();
            $client->setName('Utilisateur ' . $user->getEmail());
            $client->setAddress('Adresse à compléter');
            $client->setUser($user);

            $this->entityManager->persist($client);
            $io->text(sprintf('Client créé pour: %s', $user->getEmail()));
        }

        $this->entityManager->flush();
        $io->success('Tous les clients manquants ont été créés.');

        return Command::SUCCESS;
    }
}
