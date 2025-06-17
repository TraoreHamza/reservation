<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Location;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class LocationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $location = new Location();
        $location
            ->setCity($faker->city())
            ->setDepartment($faker->department())
            ->setNumber($faker->numberBetween(1, 100))
            ->setState($faker->state())
        ;
        $manager->persist($location);

        $manager->flush();
    }
}
