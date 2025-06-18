<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use App\Entity\User;
use App\Entity\Favorite;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FavoriteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        //  Recuperation des room nouvellement crées
        $room = [];
        for ($i = 0; $i < 10; $i++) {
            $room[] = $this->getReference('ROOM_' . $i, Room::class);
        }
         //  Recuperation des utilisateur nouvellement crées
        $users = [];
        for($i = 0; $i < 10; $i++) {
            $users[] = $this->getReference('USER_' . $i, User::class);
        }

        for ($i = 0; $i < 10; $i++) {
        $favorite = new Favorite();
        $favorite
            ->setaddedAt(new \DateTimeImmutable())
            ->setRoom($faker->randomElement($room)) // Associe une room aléatoire
            ->setUsers($faker->randomElement($users)) // Associe un utilisateur aléatoire
            
        ;
        $manager->persist($favorite);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            RoomFixtures::class,
            UserFixtures::class,
        ];
    }
}
