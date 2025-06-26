<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    { 
        $faker = Factory::create('fr_FR');

        //  Recuperation des utilisateurs crées
        $users = [];
        for ($i = 0; $i < 50; $i++) {
            $users[] = $this->getReference('USER_' . $i, User::class);
        }

        //  Recuperation des room nouvellement crées
        $rooms = [];
        for ($i = 0; $i < 10; $i++) {
            $room[] = $this->getReference('ROOM_' . $i, Room::class);
        }

        // Création d'un review par room
        foreach($rooms as $item) {
            $review = new Review();
            $review
                ->setStar($faker->numberBetween(1,5))
                ->setContent($faker->text())
                ->setRoom($item) // Associe une room aléatoire
                ->setAuthor($faker->randomElement($users))
            ;
            $manager->persist($review);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            RoomFixtures::class,
            UserFixtures::class
        ];
    }
}
