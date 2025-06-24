<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Client;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {}
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création de l'utilisateur administrateur
        $adminUser = new User();
        $adminUser
            ->setEmail('admin@sallevenue.com')
            ->setPassword($this->hasher->hashPassword($adminUser, 'admin'))
            ->setRoles(['ROLE_ADMIN'])
            ->setWarning(0)
            ->setIsBanned(false)
            ->setIsActive(true)
            ->setIsVerified(true)
        ;
        $manager->persist($adminUser);
        $this->addReference('ADMIN_USER', $adminUser);

        // Création du client admin
        $adminClient = new Client();
        $adminClient
            ->setName('Administrateur')
            ->setAddress('123 Rue de l\'Administration, 75001 Paris')
            ->setUser($adminUser)
        ;
        $manager->persist($adminClient);
        $this->addReference('ADMIN_CLIENT', $adminClient);

        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user
                ->setEmail($faker->email())
                ->setPassword($this->hasher->hashPassword($user, 'admin'))
                ->setWarning($faker->numberBetween(0, 1))
                ->setIsBanned($faker->boolean(80))
                ->setIsActive($faker->boolean(80))
            ;
            $manager->persist($user);
            $this->addReference('USER_' . $i, $user);

            $client = new Client();
            $client
                ->setName($faker->name())
                ->setAddress($faker->address())
                ->setUser($user)
            ;

            $manager->persist($client);
            $this->addReference('CLIENT_' . $i, $client);
        }

        $manager->flush();
    }
}
