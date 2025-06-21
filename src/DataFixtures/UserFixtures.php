<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
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
                ->setCreated_at(new \DateTimeImmutable())
                ->setUpdated_at(new \DateTimeImmutable())
            ;
            $manager->persist($user);
            $this->addReference('USER_' . $i, $user);
        }

        $manager->flush();
    }
}
