<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {  
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {
        $user = new User();
        $user
            ->setEmail($faker->email())
            ->setPassword($faker->password())
            ->setWarning($faker->numberBetween(0, 1, 2, 3))
            ->setIsBanned($faker->boolean(56))
            ->setIsActive($faker->boolean(75))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
        ;
        $manager->persist($user);
        $this->addReference('user_' . $i, $user);
    }

        $manager->flush();
    }
}
