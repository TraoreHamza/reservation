<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class RoomFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $room = new Room();
        $room
            ->setName($faker->word())
            ->setCapacity($faker->numberBetween())
            ->setDescription($faker->sentence())
            ->setIsAvailable($faker->boolean())
        ;
        $manager->persist($room);
        $this->addReference('ROOM_', $room);

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
        
        ];
    }
}
