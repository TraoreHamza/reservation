<?php

namespace App\DataFixtures;

use App\Entity\Location;
use Faker\Factory;
use App\Entity\Room;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RoomFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $rooms = [
            'Salle Victor Hugo',
            'Salle Eiffel',
            'Salle Pasteur',
            'Salle Curie',
            'Salle Molière',
            'Salle Voltaire',
            'Salle Zola',
            'Salle Diderot',
            'Salle Monet',
            'Salle Renoir',
            'Salle Rodin',
            'Salle Balzac',
            'Salle Proust',
            'Salle Camus',
            'Salle Sartre',
            'Salle Baudelaire',
            'Salle Verne',
            'Salle Colette',
            'Salle Cézanne',
            'Salle Gaumont',
            'Salle Lumière',
            'Salle Turing',
            'Salle Descartes',
            'Salle Rousseau',
            'Salle De Vinci',
            'Salle Picasso',
            'Salle Ravel',
            'Salle Debussy',
            'Salle Chopin',
            'Salle Bizet',
            'Salle Offenbach',
            'Salle Gounod',
            'Salle Berlioz',
            'Salle Saint-Saëns',
            'Salle Fauré',
            'Salle Poulenc',
            'Salle Satie',
            'Salle Vivaldi',
            'Salle Mozart',
            'Salle Beethoven',
            'Salle Bach',
            'Salle Schubert',
            'Salle Liszt',
            'Salle Mendelssohn',
            'Salle Brahms',
            'Salle Wagner',
            'Salle Strauss',
            'Salle Mahler',
            'Salle Rameau',
            'Salle Lully',
            'Salle Couperin',
        ];

        $descriptions = [
            "Salle lumineuse idéale pour les réunions d'équipe.",
            "Espace moderne équipé d'un vidéoprojecteur.",
            "Salle polyvalente adaptée aux formations et ateliers.",
            "Salle calme avec vue sur le jardin, parfaite pour la concentration.",
            "Espace convivial pour les séances de brainstorming.",
            "Salle de conférence avec système audio intégré.",
            "Salle équipée pour visioconférences internationales.",
            "Ambiance chaleureuse, idéale pour des petits groupes.",
            "Grande salle modulable pour événements et séminaires.",
            "Espace équipé de tableaux blancs et paperboards.",
            "Salle insonorisée pour réunions confidentielles.",
            "Salle de travail avec accès Wi-Fi haut débit.",
            "Salle spacieuse avec coin détente.",
            "Salle adaptée aux ateliers créatifs et artistiques.",
            "Espace de réunion avec lumière naturelle abondante.",
            "Salle équipée d’un écran interactif tactile.",
            "Salle de formation avec 20 postes informatiques.",
            "Espace réservé aux réunions de direction.",
            "Salle confortable avec fauteuils ergonomiques.",
            "Salle équipée pour projections vidéo HD.",
            "Salle accessible aux personnes à mobilité réduite.",
            "Espace modulable pour réunions et banquets.",
            "Salle équipée d’un système de climatisation.",
            "Salle à l’acoustique optimisée pour la musique.",
            "Salle de réunion avec kitchenette attenante.",
            "Salle décorée dans un style contemporain.",
            "Espace chaleureux pour réunions informelles.",
            "Salle avec accès direct à une terrasse extérieure.",
            "Salle idéale pour formations en petits groupes.",
            "Salle équipée de prises électriques individuelles.",
            "Salle de réunion avec tableau interactif.",
            "Salle avec vue panoramique sur la ville.",
            "Espace de travail collaboratif et flexible.",
            "Salle adaptée aux visioconférences et webinaires.",
            "Salle dotée d’un système de sonorisation performant.",
            "Salle de réunion avec mobilier modulable.",
            "Espace sécurisé pour réunions confidentielles.",
            "Salle équipée de stores occultants.",
            "Salle avec accès direct au parking.",
            "Salle de réunion avec bibliothèque intégrée.",
            "Salle lumineuse avec grandes baies vitrées.",
            "Salle de conférence avec scène et pupitre.",
            "Salle avec espace de rangement pour matériel.",
            "Salle équipée de prises réseau RJ45.",
            "Salle de réunion avec espace lounge.",
            "Salle adaptée aux ateliers de formation pratique.",
            "Espace convivial pour pauses café et échanges.",
            "Salle avec accès privatif et sécurisé.",
            "Salle de réunion avec système de réservation en ligne.",
            "Salle équipée pour projections 4K.",
            "Salle avec coin vestiaire pour les participants.",
        ];
        $imageFilenames = [
            'salle_victor_hugo.png',
            'brit-hotel-saint-meen-le-grand-adresse.jpeg',
            'urban-soccer-rennes-vern-seiche.jpg',
            'cap-events-la-reposee-3.jpg',
            'cgr-vry-17.jpg',
            'espace-de-conferences-iris-10.jpg',
            'espace-de-conferences-iris-10.jpg',
            'mega-cgr-blagnac-16.jpg',
            'casino-de-la-roche-posay.jpg',
            'docks-de-paris-7.jpg',
            'euro-meeting-center-8.jpg',
            'mas-saint-gabriel-17.jpg',
        ];

        $locations = [];
        for ($i = 0; $i < 10; $i++) {
            $locations[] = $this->getReference('LOCATION_' . $i, Location::class);
        }

        $i = 0;
        foreach ($rooms as $index => $roomName) {
            $room = new Room();
            $imageFilename = $imageFilenames[$index % count($imageFilenames)];
            $room
                ->setName($roomName)
                ->setCapacity($faker->numberBetween(1, 100))
                ->setImage($imageFilename) // On stocke juste le nom du fichier !
                ->setDescription($descriptions[$index])
                ->setIsAvailable($faker->boolean(80))
                ->setDailyRate($faker->numberBetween(100, 2500)) // Prix journalier entre 100 et 2500 euros
                ->setLocation($faker->randomElement($locations)) // Associer une location aléatoire
            ;
            $manager->persist($room);
            $this->addReference('ROOM_' . $i, $room);

            if ($i % 100 === 0) {
                $manager->flush();
            }
            $i++;
        }
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            LocationFixtures::class,
        ];
    }
}
