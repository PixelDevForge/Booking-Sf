<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;

use App\Entity\Image;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('FR-fr');
        



        for( $i = 0; $i < 30; $i++ ) {

            $ad = new Ad();
            $title = $faker->sentence();
            $coverImage = "https://picsum.photos/1000/500";
            $introduction = $faker->paragraph(2);
            $content = "<p>" . implode("</p><p>", $faker->paragraphs(5)) . "</p>";
            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)   
                ->setContent($content)
                ->setPrice(mt_rand(30,200))
                ->setRooms(mt_rand(1,6))
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
