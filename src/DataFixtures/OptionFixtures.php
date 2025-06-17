<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Option;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class OptionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $option = new Option();
        $option
            ->setName($faker->word(2))
        ;
        $manager->persist($option);

        $manager->flush();
    }
}
