<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RoomFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {   
        $faker = Factory::create('fr_FR');
        
        for ($i = 0; $i < 10; $i++) {
            $room = new Room();
            $room
                ->setName($faker->word())
                ->setCapacity($faker->numberBetween(1, 100))
                ->setDescription($faker->sentence())
                ->setIsAvailable($faker->boolean())
            ;
            $manager->persist($room);
            $this->addReference('ROOM_' . $i, $room);
        }
        $manager->flush();
    }
}
