<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use App\Entity\Option;
use App\DataFixtures\RoomFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OptionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $options = [
            'Pause-café incluse',
            'Restauration',
            'Nettoyage après usage',
            'Accueil sur place',
            'Assistance technique',
        ];

        $room = [];
        for ($i = 0; $i < 50; $i++) {
            $room[] = $this->getReference('ROOM_' . $i, Room::class);
        }

        foreach ($options as $optionKey => $optionName) {
            $option = new Option();
            $option->setName($optionName);

            // Associer aléatoirement entre 2 et 4 salles
            $randomRooms = $faker->randomElements($room, $faker->numberBetween(7, 10));
            foreach ($randomRooms as $r) {
                $option->addRoom($r);
            }

            $manager->persist($option);
        }


        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            RoomFixtures::class,
        ];
    }
}
