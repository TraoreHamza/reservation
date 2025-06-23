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
            'Vidéoprojecteur',
            'Tableau blanc',
            'Paperboard',
            'Connexion Wi-Fi',
            'Climatisation',
            'Chauffage',
            'Système audio',
            'Micro sans fil',
            'Ordinateur portable',
            'Télévision',
            'Salle insonorisée',
            'Accès PMR',
            'Espace café',
            'Fontaine à eau',
            'Service traiteur',
            'Chaises supplémentaires',
            'Tables modulables',
            'Lumière naturelle',
            'Rideaux occultants',
            'Prises électriques',
            'Prises réseau RJ45',
            'Imprimante',
            'Scanner',
            'Photocopieuse',
            'Terrasse extérieure',
            'Parking privé',
            'Vestiaire',
            'Espace détente',
            'Salle de pause',
            'Accueil personnalisé',
        ];

        $room = [];
        for ($i = 0; $i < 10; $i++) {
            $room[] = $this->getReference('ROOM_' . $i, Room::class);
        }
        foreach ($options as $index => $optionName) {
            $option = new Option();
            $option
                ->setName($optionName)
                ->addRoom($faker->randomElement($room)) // Associe une room aléatoire
            ;
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
