<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Equipment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class EquipmentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $equipment = new Equipment();
        $equipment
            ->setName($faker->word(2))
            ->setType($faker->word(2))
        ;
        $manager->persist($equipment);


        $manager->flush();
    }
}
