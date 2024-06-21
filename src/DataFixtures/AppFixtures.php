<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;

use App\Entity\User;
use App\Entity\Image;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        
        $this->hasher=$hasher;

    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('FR-fr');
        $users = [];
        $genres = ['male','female'];
        
        // User

        for( $i = 0; $i <=10; $i++ ) {
            
            $user = new User();
            $genre = $faker->randomElement($genres);
            $avatar = 'https://randomuser.me/api/portraits/';
            $avatarId = $faker->numberBetween(1,99).'.jpg';
            $avatar .= ($genre == 'male' ? 'men/' : 'women/'). $avatarId .'';
            $hash = $this -> hasher -> hashPassword($user,'password');


            
            $description = "<p>" . implode("</p><p>", $faker->paragraphs(5)) . "</p>";
            $user -> setDescription($description)
                  -> setFirstname($faker->firstName)
                  -> setLastname($faker->lastName)
                  -> setEmail($faker->email)
                  -> setIntroduction($faker->sentence(3))
                  -> setHash($hash)
                  -> setAvatar($avatar);
            $manager -> persist($user);
            $users[] = $user; 
            

        };



        // Annonces


        for( $i = 0; $i < 30; $i++ ) {

            $ad = new Ad();
            $title = $faker->sentence();
            $coverImage = "https://picsum.photos/1000/500";
            $introduction = $faker->paragraph(2);
            $content = "<p>" . implode("</p><p>", $faker->paragraphs(5)) . "</p>";
            $user = $users[mt_rand(0, count($users) -1)];
            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)   
                ->setContent($content)
                ->setPrice(mt_rand(30,200))
                ->setRooms(mt_rand(1,6))
                ->setAuthor($user)
                ;
            $manager->persist($ad);


            for( $j = 0; $j <=mt_rand(2,5);$j++ ) {
                $image = new Image();
                $image->setUrl("https://picsum.photos/1000/500")
                      ->setCaption($faker->sentence())
                      ->setAd($ad)
                      ;
                $manager->persist($image);
            }



        };

        $manager->flush();

    }
}
