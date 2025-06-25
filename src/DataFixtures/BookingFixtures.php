<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use App\Entity\User;
use App\Entity\Booking;
use App\DataFixtures\RoomFixtures;
use App\DataFixtures\UserFixtures;
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
        $users = [];
        for ($i = 0; $i < 50; $i++) {
            $users[] = $this->getReference('USER_' . $i, User::class);
        }

        for ($i = 0; $i < 10; $i++) {
            $user = $faker->randomElement($users);
            $booking = new Booking();
            $startDate = $faker->dateTimeBetween('-1 week', '+1 week'); // Date de début aléatoire
            $endDate = $faker->dateTimeBetween($startDate, '+1 week'); // Date de fin après la date de début

            $booking
                ->setStatus($faker->randomElement([
                    "confirmed",
                    "pending",
                    "cancelled",
                ]))
                ->setStartDate(\DateTimeImmutable::createFromMutable($startDate)) // Conversion en DateTimeImmutable
                ->setEndDate(\DateTimeImmutable::createFromMutable($endDate)) // Conversion en DateTimeImmutable
                ->setRoom($faker->randomElement($room)) // Associe une room aléatoire
                ->setClient($user->getClient());

            $manager->persist($booking);
            $this->addReference('BOOKING_' . $i, $booking); // Ajout de la référence pour les autres fixtures
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            RoomFixtures::class,
        ];
    }
}