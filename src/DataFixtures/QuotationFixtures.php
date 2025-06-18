<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use App\Entity\Client;
use App\Entity\Quotation;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class QuotationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        //  Recuperation des room nouvellement crées
        $room = [];
        for ($i = 0; $i < 10; $i++) {
            $room[] = $this->getReference('ROOM_' . $i, Room::class);
        }

        // Recuperation des client nouvellement crées
        $client = [];
        for ($i = 0; $i < 10; $i++) {
            $client[] = $this->getReference('CLIENT_' . $i, Client::class);
        }
        // Création d'une nouvelle instance de Quotation
        for ($i = 0; $i < 10; $i++) {
        $quotation = new Quotation();
        $quotation
            ->setPrice($faker->randomFloat(2, 100, 1000))
            ->setDate($faker->date())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setRoom($faker->randomElement($room)) // Associe une room aléatoire
            ->setClient($faker->randomElement($client)) // Associe un client aléatoire
        ;
        $manager->persist($quotation);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            RoomFixtures::class,
            ClientFixtures::class,
        ];
    }
}
