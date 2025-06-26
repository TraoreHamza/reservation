<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use App\Entity\Equipment;
use App\DataFixtures\RoomFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EquipmentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $equipments = [
            ['type' => 'Audiovisuel', 'name' => 'Vidéoprojecteur'],
            ['type' => 'Matériel',     'name' => 'Tableau blanc'],
            ['type' => 'Réseaux',      'name' => 'Wi-Fi'],
            ['type' => 'Audiovisuel',  'name' => 'Microphone'],
            ['type' => 'Audiovisuel',  'name' => 'Enceinte audio'],
            ['type' => 'Audiovisuel',  'name' => 'Caméra de visio'],
            ['type' => 'Matériel',     'name' => 'Climatisation'],
            ['type' => 'Matériel',     'name' => 'Ordinateur fourni'],
        ];

        // Récupération des rooms créées
        $rooms = [];
        for ($i = 0; $i < 50; $i++) {
            $rooms[] = $this->getReference('ROOM_' . $i, Room::class);
        }

        foreach ($equipments as $data) {
            $equipment = new Equipment();
            $equipment
                ->setName($data['name'])
                ->setType($data['type'])
                ->addRoom($faker->randomElement($rooms))
                ;

            $random = $faker->randomElements($rooms, $faker->numberBetween(7, 10));
            foreach ($random as $r) {
                $equipment->addRoom($r);
            }

            $manager->persist($equipment);

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
