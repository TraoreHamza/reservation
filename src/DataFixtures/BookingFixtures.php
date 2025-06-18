<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Booking;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class BookingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $booking = new Booking();
        $booking
            ->setStatus($faker->randomElement(['pending', 'confirmed', 'cancelled']))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setStartDate(new \DateTimeImmutable())
            ->setEndDate(new \DateTimeImmutable())
        ;
        $manager->persist($booking);

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,
            EquipmentFixtures::class,
            OptionFixtures::class,
            FavoriteFixtures::class,
        ];
    }
}
