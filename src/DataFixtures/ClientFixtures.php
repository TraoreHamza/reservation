<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Client;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ClientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $faker = Factory::create('fr_FR');

        // //  Recuperation des utilisateurs crées
        // $users = [];
        // for ($i = 0; $i < 50; $i++) {
        //     $room[] = $this->getReference('USER_' . $i, User::class);
        // }

        // $i = 0;
        // foreach ($users as $item) {
        //     $client = new Client();
        //     $client
        //         ->setName($faker->name())
        //         ->setAddress($faker->address())
        //         ->setUser($item)
        //     ;

        //     $manager->persist($client);
        //     $this->addReference('CLIENT_' . $i, $client);

        //     $i++;
        // }

        // $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
