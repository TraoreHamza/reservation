<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use App\Entity\Client;
use App\Entity\Booking;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BookingFixtures extends Fixture implements DependentFixtureInterface
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
        $booking = new Booking();
        $booking
            ->setStatus($faker->randomElement())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setStartDate(new \DateTimeImmutable())
            ->setEndDate(new \DateTimeImmutable())
            ->setRoom($faker->randomElement($room)) // Associe une room aléatoire
            ->setClient($faker->randomElement($client))

// Associe un client al 
        ;
        $manager->persist($booking);

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,
            RoomFixtures::class,
        ];
    }
}
