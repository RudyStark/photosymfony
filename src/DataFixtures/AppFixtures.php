<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Photo;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Tableau pour stocker les références aux objets Tag
        $tags = [];

        for ($i = 0; $i < 10; $i++) {
            $tag = new Tag();
            $tag->setName($faker->unique()->word);

            $manager->persist($tag);
            $tags[] = $tag;
        }

        for ($i = 0; $i < 30; $i++) {
            $photo = new Photo();
            $photo->setTitle($faker->sentence);
            $photo->setUrl($faker->imageUrl);
            $photo->setPrice($faker->randomFloat(2, 0, 100));
            $photo->setDescription($faker->paragraph);
            $photo->setMetaInfo([]);
            $photo->setCreatedAt(new \DateTimeImmutable("now"));

            // Ajout de 1 à 3 tags aléatoires à chaque photo
            for ($j = 0; $j < mt_rand(1, 3); $j++) {
                $photo->addTag($tags[array_rand($tags)]);
            }

            $manager->persist($photo);
        }

        $manager->flush();
    }
}


