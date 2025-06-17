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
            ->setDepartment('')
            ->setNumber($faker->numberBetween())
            ->setState($faker->region())
        ;
        $manager->persist($location);

        $manager->flush();
    }
}
