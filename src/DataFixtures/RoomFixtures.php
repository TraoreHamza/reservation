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

class RoomFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
    ){}
    public function load(ObjectManager $manager): void
    {   
        $slugger = new Slugify();
        $faker = Factory::create('fr_FR');
        // Admin
        $admin = new User();
        $admin
            ->setEmail('admin@admin.fr')
            ->setPassword($this->hasher->hashPassword($admin, 'admin'))
            ->setWarning(0)
            ->setRoles(['ROLE_ADMIN'])
        ;

        $manager->persist($admin);
        $manager->flush(); // Admin enregistré en base de données
        
        for ($i = 0; $i < 10; $i++) {
            $room = new Room();
            $room
                ->setName($faker->word())
                ->setCapacity($faker->numberBetween(1, 100))
                ->setDescription($faker->sentence())
                ->setIsAvailable($faker->boolean())
                ->setImage($faker->imageUrl()) // Génère une URL d'image aléatoire
            ;
            $manager->persist($room);
            $this->addReference('ROOM_' . $i, $room);
        }
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class, // Assure que les utilisateurs sont créés avant les rooms
        ];
    }
}
