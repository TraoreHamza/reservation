<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use App\Entity\Location;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LocationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        //Recuperation des room nouvellement crées
        $room = [];
        for ($i = 0; $i < 10; $i++) {
            $room[] = $this->getReference('ROOM_' . $i, Room::class);
        }
        $location = new Location();
        $location
            ->setCity($faker->city())
            ->setDepartment('')
            ->setNumber($faker->numberBetween())
            ->setState($faker->region())
            ->addRoom($faker->randomElement($room))
        ;
        $manager->persist($location);

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            RoomFixtures::class,
        ];
    }
}
