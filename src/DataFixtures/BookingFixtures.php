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

        // Création de réservations avec des dates variées pour tester les notifications
        for ($i = 0; $i < 15; $i++) {
            $user = $faker->randomElement($users);
            $booking = new Booking();

            // Dates variées pour tester les notifications
            $dateRange = match ($i) {
                0, 1, 2 => ['-10 days', '-8 days'], // Réservations anciennes en attente (urgentes)
                3, 4, 5 => ['-2 days', '+1 day'],  // Réservations récentes en attente
                6, 7, 8 => ['+2 days', '+5 days'], // Réservations à venir
                9, 10 => ['+1 week', '+2 weeks'],  // Réservations futures
                default => ['-1 week', '+1 week']  // Réservations normales
            };

            $startDate = $faker->dateTimeBetween($dateRange[0], $dateRange[1]);
            // S'assurer que la date de fin est après la date de début
            $endDate = $faker->dateTimeBetween($startDate, (clone $startDate)->modify('+3 days'));

            // Statuts variés pour tester le code couleur
            $status = match ($i) {
                0, 1, 2, 3, 4, 5 => 'pending',    // En attente (pour tester les notifications)
                6, 7, 8 => 'confirmed',           // Confirmées
                9, 10 => 'cancelled',             // Annulées
                default => $faker->randomElement(['confirmed', 'pending', 'cancelled'])
            };

            $booking
                ->setStatus($status)
                ->setStartDate(\DateTimeImmutable::createFromMutable($startDate))
                ->setEndDate(\DateTimeImmutable::createFromMutable($endDate))
                ->setRoom($faker->randomElement($room))
                ->setClient($user->getClient())
                ->setUser($user);

            $manager->persist($booking);
            $this->addReference('BOOKING_' . $i, $booking);
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
