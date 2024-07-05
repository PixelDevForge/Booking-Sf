<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    // Déclaration de la variable pour le hasher de mot de passe
    private $hasher;

    // Constructeur pour injecter le hasher de mot de passe
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    // Méthode principale pour charger les fixtures
    public function load(ObjectManager $manager): void
    {
        // Initialisation de Faker pour générer des données aléatoires en français
        $faker = Factory::create('FR-fr');
        
        // Création du rôle administrateur
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        // Création d'un utilisateur administrateur
        $adminUser = new User();
        $adminUser->setFirstname('David')
                  ->setLastname('Grappu')
                  ->setEmail('david.grappu@outlook.fr')
                  ->setHash($this->hasher->hashPassword($adminUser, 'password'))
                  ->setAvatar('https://randomuser.me/api/portraits/men/55.jpg')
                  ->setIntroduction($faker->sentence(3))
                  ->setDescription("<p>" . implode("</p><p>", $faker->paragraphs(5)) . "</p>")
                  ->addUserRole($adminRole);
        $manager->persist($adminUser);

        // Tableau pour stocker les utilisateurs générés
        $users = [];
        $genres = ['male', 'female'];

        // Boucle pour créer plusieurs utilisateurs
        for ($i = 0; $i <= 10; $i++) {
            $user = new User();
            $genre = $faker->randomElement($genres);

            // Génération d'un avatar aléatoire en fonction du genre
            $avatar = 'https://randomuser.me/api/portraits/';
            $avatarId = $faker->numberBetween(1, 99) . '.jpg';
            $avatar .= ($genre == 'male' ? 'men/' : 'women/') . $avatarId;

            // Hachage du mot de passe
            $hash = $this->hasher->hashPassword($user, 'password');

            // Création d'une description aléatoire
            $description = "<p>" . implode("</p><p>", $faker->paragraphs(5)) . "</p>";

            // Configuration des propriétés de l'utilisateur
            $user->setDescription($description)
                 ->setFirstname($faker->firstName)
                 ->setLastname($faker->lastName)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence(3))
                 ->setHash($hash)
                 ->setAvatar($avatar);
            $manager->persist($user);

            // Ajout de l'utilisateur au tableau
            $users[] = $user; 
        }

        // Boucle pour créer plusieurs annonces
        for ($i = 0; $i < 30; $i++) {
            $ad = new Ad();
            $title = $faker->sentence();
            $coverImage = "https://picsum.photos/1000/500";
            $introduction = $faker->paragraph(2);
            $content = "<p>" . implode("</p><p>", $faker->paragraphs(5)) . "</p>";

            // Sélection aléatoire d'un utilisateur auteur
            $user = $users[mt_rand(0, count($users) - 1)];

            // Configuration des propriétés de l'annonce
            $ad->setTitle($title)
               ->setCoverImage($coverImage)
               ->setIntroduction($introduction)   
               ->setContent($content)
               ->setPrice(mt_rand(30, 200))
               ->setRooms(mt_rand(1, 6))
               ->setAuthor($user);
            $manager->persist($ad);

            // Boucle pour créer plusieurs images associées à l'annonce
            for ($j = 0; $j <= mt_rand(2, 5); $j++) {
                $image = new Image();
                $image->setUrl("https://picsum.photos/1000/500")
                      ->setCaption($faker->sentence())
                      ->setAd($ad);
                $manager->persist($image);
            }
        }

        // Enregistrement de toutes les entités en base de données
        $manager->flush();
    }
}
