<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Quotation;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class QuotationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $quotation = new Quotation();
        $quotation
            ->setPrice($faker->randomFloat(2, 100, 1000))
            ->setDate($faker->date())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
        ;
        $manager->persist($quotation);

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,
        ];
    }
}
