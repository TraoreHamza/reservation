<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Client;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $client = new Client();
        $client
            ->setName($faker->name())
            ->setAddresse($faker->address())
            
        ;
        $manager->persist($client);
        

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [];
    }
}
