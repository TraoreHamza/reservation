<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Client;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {}
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {
            $name = $faker->firstName();

            $user = new User();
            $user
                ->setEmail($this->slugify($name) . '@' . $faker->freeEmailDomain())
                ->setPassword($this->hasher->hashPassword($user, 'admin'))
                ->setWarning($faker->numberBetween(0, 1))
                ->setIsBanned($faker->boolean(80))
                ->setIsActive($faker->boolean(80))
            ;
            $manager->persist($user);
            $this->addReference('USER_' . $i, $user);

            $client = new Client();
            $client
                ->setName($name)
                ->setAddress($faker->address())
                ->setUser($user)
            ;

            $manager->persist($client);
            $this->addReference('CLIENT_' . $i, $client);
        }

        $admin = new User();
        $admin
            ->setEmail('admin@admin.fr')
            ->setPassword($this->hasher->hashPassword($admin, 'admin'))
            ->setRoles(["ROLE_ADMIN"])
        ;
        $manager->persist($admin);


        $client = new Client();
        $client
            ->setName('Admin')
            ->setAddress('38 rue. de la station 95130')
            ->setUser($admin)
        ;

        $manager->persist($admin);

        $test = new User();
        $test
            ->setEmail('test@test.fr')
            ->setPassword($this->hasher->hashPassword($test, 'test'))
        ;
        $manager->persist($test);


        $client = new Client();
        $client
            ->setName('Test')
            ->setAddress('38 rue. de la station 95130')
            ->setUser($test)
        ;

        $manager->persist($test);

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
