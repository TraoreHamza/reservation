<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use App\Entity\Equipment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class EquipmentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $equipements = [
            'Vidéoprojecteur',
            'Écran de projection',
            'Tableau interactif',
            'Tableau blanc',
            'Paperboard',
            'Système de visioconférence',
            'Ordinateur portable',
            'Télévision',
            'Enceintes audio',
            'Microphone sans fil',
            'Caméra de surveillance',
            'Imprimante',
            'Scanner',
            'Photocopieuse',
            'Routeur Wi-Fi',
            'Prises électriques multiples',
            'Prises réseau RJ45',
            'Climatisation',
            'Chauffage d’appoint',
            'Lumière LED réglable',
            'Webcam HD',
            'Lecteur DVD/Blu-ray',
            'Télécommande universelle',
            'Chargeur universel',
            'Station de recharge USB',
            'Distributeur de boissons',
            'Fontaine à eau',
            'Machine à café',
            'Mini-frigo',
            'Vestiaire mobile',
        ];

        $typeEquipements = [
            'Mobilier' => [
                'Chaises',
                'Tables',
                'Fauteuils',
                'Estrades',
                'Podiums',
                'Pupitres',
                'Vestiaires',
                'Cloisons amovibles',
                'Tables bistro',
                'Canapés',
            ],
            'Audiovisuel' => [
                'Vidéoprojecteur',
                'Écran de projection',
                'Téléviseur',
                'Système de sonorisation',
                'Microphone sans fil',
                'Haut-parleurs',
                'Caméra de visioconférence',
                'Tableau interactif',
                'Ordinateur de présentation',
                'Webcam HD',
            ],
            'Éclairage' => [
                'Lumières LED',
                'Spots directionnels',
                'Lampes d’ambiance',
                'Éclairage de scène',
                'Projecteurs',
            ],
            'Connectique & Réseau' => [
                'Prises électriques',
                'Prises réseau RJ45',
                'Routeur Wi-Fi',
                'Multiprises',
                'Câbles HDMI/USB',
                'Adaptateurs divers',
            ],
            'Confort & Accessibilité' => [
                'Climatisation',
                'Chauffage',
                'Rideaux occultants',
                'Accès PMR',
                'Fontaine à eau',
                'Distributeur de boissons',
                'Machine à café',
                'Réfrigérateur',
                'Espace détente',
            ],
            'Outils de réunion' => [
                'Tableau blanc',
                'Paperboard',
                'Tableau à épingles',
                'Pointeur laser',
                'Imprimante',
                'Scanner',
                'Photocopieuse',
            ],
            'Sécurité' => [
                'Détecteur de fumée',
                'Extincteur',
                'Trousse de premiers secours',
                'Alarme incendie',
                'Caméra de surveillance',
            ],
        ];



        //  Recuperation des room nouvellement crées
        $room = [];
        for ($i = 0; $i < 10; $i++) {
            $room[] = $this->getReference('ROOM_' . $i, Room::class);
        }
        foreach ($typeEquipements as $type => $equipments) {
            foreach ($equipements as $equipementName) {
            $equipment = new Equipment();
            $equipment
                ->setName($equipementName)
                ->setType($type)
            ;
            $manager->persist($equipment);
            }
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
