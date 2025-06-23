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

        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user
                ->setEmail($faker->email())
                ->setPassword($this->hasher->hashPassword($user, 'admin'))
                ->setWarning($faker->numberBetween(0, 3))
                ->setIsBanned($faker->boolean(56))
                ->setIsActive($faker->boolean(75))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
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
