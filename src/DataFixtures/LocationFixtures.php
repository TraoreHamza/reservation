<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use App\Entity\Location;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LocationFixtures extends Fixture 
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');



        // Chargement des données du CSV
        $csvFile = __DIR__ . '/communes-francaises-light.csv';
        $csvData = array_map('str_getcsv', file($csvFile));
        array_shift($csvData); // Enlève l'en-tête

        // Boucle sur les données du CSV
        $i = 0;
        foreach ($csvData as $index => $row) {
            $location = new Location();
            $location->setCity($row[0]);          // Commune
            $location->setNumber($row[1]);        // Département (numéro)
            $location->setDepartment($row[2]);    // Département (nom)
            $location->setState($this->slugify($row[3])); // Région



            $manager->persist($location);
            $this->addReference('LOCATION_' . $i, $location);
            $i++;
        }

        $manager->flush();
    }

    private function slugify(string $text): string
    {
        // Remplacer les caractères spéciaux
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        // Convertir en minuscules
        $text = strtolower($text);
        // Remplacer tout ce qui n'est pas lettre ou nombre par un tiret
        $text = preg_replace('/[^a-z0-9]/', '-', $text);
        // Supprimer les tirets multiples
        $text = preg_replace('/-+/', '-', $text);
        // Supprimer les tirets au début et à la fin
        $text = trim($text, '-');

        return $text;
    }


}
