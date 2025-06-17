<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Equipement;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class EquipementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $equipement = new Equipement();
        $equipement
            ->setName($faker->word(2))
            ->setType($faker->word(2))
        ;
        $manager->persist($equipement);

        $manager->flush();
    }
}
