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
        $city = [
            'Paris',
            'Marseille',
            'Lyon',
            'Toulouse',
            'Nice',
            'Nantes',
            'Strasbourg',
            'Montpellier',
            'Bordeaux',
            'Lille',
            'Rennes',
            'Reims',
            'Le Havre',
            'Saint-Étienne',
            'Toulon',
            'Grenoble',
            'Dijon',
            'Angers',
            'Nîmes',
            'Villeurbanne',
            'Saint-Denis',
            'Aix-en-Provence',
            'Clermont-Ferrand',
            'Le Mans',
            'Amiens',
            'Tours',
            'Limoges',
            'Annecy',
            'Perpignan',
            'Boulogne-Billancourt',
            'Metz',
            'Besançon',
            'Orléans',
            'Saint-Denis (La Réunion)',
            'Argenteuil',
            'Rouen',
            'Montreuil',
            'Mulhouse',
            'Caen',
            'Nancy',
            'Saint-Paul',
            'Roubaix',
            'Tourcoing',
            'Nanterre',
            'Avignon',
            'Vitry-sur-Seine',
            'Créteil',
            'Poitiers',
            'Aubervilliers',
            'Versailles',
        ];

        $departements = [
            'Ain',
            'Aisne',
            'Allier',
            'Alpes-de-Haute-Provence',
            'Hautes-Alpes',
            'Alpes-Maritimes',
            'Ardèche',
            'Ardennes',
            'Ariège',
            'Aube',
            'Aude',
            'Aveyron',
            'Bouches-du-Rhône',
            'Calvados',
            'Cantal',
            'Charente',
            'Charente-Maritime',
            'Cher',
            'Corrèze',
            'Corse-du-Sud',
            'Haute-Corse',
            'Côte-d\'Or',
            'Côtes-d\'Armor',
            'Creuse',
            'Dordogne',
            'Doubs',
            'Drôme',
            'Eure',
            'Eure-et-Loir',
            'Finistère',
            'Gard',
            'Haute-Garonne',
            'Gers',
            'Gironde',
            'Hérault',
            'Ille-et-Vilaine',
            'Indre',
            'Indre-et-Loire',
            'Isère',
            'Jura',
            'Landes',
            'Loir-et-Cher',
            'Loire',
            'Haute-Loire',
            'Loire-Atlantique',
            'Loiret',
            'Lot',
            'Lot-et-Garonne',
            'Lozère',
            'Maine-et-Loire',
        ];

        //Recuperation des room nouvellement crées
        $room = [];
        for ($i = 0; $i < 10; $i++) {
            $room[] = $this->getReference('ROOM_' . $i, Room::class);
        }

        foreach ($city as $index => $cityName) {
            $location = new Location();
            $location
                ->setCity($cityName)
                ->setDepartment($departements[$index]) // Pour éviter l'index hors limites
                ->setNumber($faker->numberBetween())
                ->setState($faker->region())
                ->addRoom($faker->randomElement($room))
            ;
            $manager->persist($location);
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
