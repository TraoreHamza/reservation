<?php

namespace App\DataFixtures;

use App\Entity\User;
use Faker\Factory;
use App\Entity\Booking;
use App\Entity\Quotation;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class QuotationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        //  Recuperation des booking nouvellement crées
        $bookings = [];
        for ($i = 0; $i < 10; $i++) {
            $bookings[] = $this->getReference('BOOKING_' . $i, Booking::class);
        }

        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = $this->getReference('USER_' . $i, User::class);
            if ($user) {
                $users[] = $user;
            }
        }

        // Création d'une nouvelle instance de Quotation
        for ($i = 0; $i < 10; $i++) {
            $quotation = new Quotation();
            $quotation
                ->setPrice($faker->numberBetween(200, 2000)) // Prix aléatoire entre 200 et 2000 euros
                ->setBooking($faker->randomElement($bookings)) // Associe un booking aléatoire
                ->setCreatedBy($faker->randomElement($users)) // Associe un utilisateur créateur
            ;
            $manager->persist($quotation);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            BookingFixtures::class,
            UserFixtures::class,
        ];
    }
}
