<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Favorite;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FavoriteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $favorite = new Favorite();
        $favorite
            ->setaddedAt(new \DateTimeImmutable())
        ;
        $manager->persist($favorite);

        $manager->flush();
    }
}
