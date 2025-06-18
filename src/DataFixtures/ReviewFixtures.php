<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Review;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ReviewFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    { 
        $faker = Factory::create('fr_FR');
        $review = new Review();
        $review
            ->setStar($faker->numberBetween())
            ->setContent($faker->text())
        ;
        $manager->persist($review);

        $manager->flush();
    }
}
