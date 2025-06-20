<?php

namespace App\DataFixtures;

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
        $booking = [];
        for ($i = 0; $i < 10; $i++) {
            $booking[] = $this->getReference('BOOKING_' . $i, Booking::class);
        }
        
        // Création d'une nouvelle instance de Quotation
        for ($i = 0; $i < 10; $i++) {
            $quotation = new Quotation();
            $quotation
                ->setPrice($faker->numberBetween(200, 2000)) // Prix aléatoire entre 200 et 2000 euros
                ->setDate($faker->date())
                ->setcreated_at(new \DateTimeImmutable())
                ->setupdated_at(new \DateTimeImmutable())
                ->setBooking($faker->randomElement($booking)) // Associe un booking aléatoire
            ;
            $manager->persist($quotation);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            BookingFixtures::class,
        ];
    }
}
