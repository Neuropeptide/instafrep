<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // récupérer tous les users existants
        $users = $manager->getRepository(User::class)->findAll();

        $nbPosts = 100;
        $posts = [];
        for ($i = 0; $i < $nbPosts; $i++) {

            $post = new Post();

            // auteur aléatoire
            $randomIndex = array_rand($users);
            /** @var int $randomIndex */
            $author = $users[$randomIndex];
            /** @var User $author */
            $post
                ->setAuthor($author)
                ->setTitle($faker->words(rand(4, 5), true))
                ->setContent($faker->realText());

            // est ce que ce Post a un parent ? (80% de chance)
            if (!empty($posts) && mt_rand(0, 10) >= 2) {
                $randomIndex = array_rand($posts);
                /** @var Post $parentPost */
                $parentPost = $posts[$randomIndex];
                $post->setParent($parentPost);

                $views = $faker->numberBetween(0, $parentPost->getViews());
                $createdAt = $faker->dateTimeBetween($parentPost->getCreatedAt());
                $publishedAt = $createdAt;

            } else {
                $views = $faker->numberBetween(0, 1000);
                $createdAt = $faker->dateTimeThisYear;
                $publishedAt = $faker->dateTimeBetween($createdAt);
            }

            $post
                ->setViews($views)
                ->setCreatedAt($createdAt)
                ->setPublishedAt($publishedAt);

            if (rand(0, 100) > 70) { // 30% de chance d'avoir été mis a jour
                $updatedAt = $faker->dateTimeBetween($createdAt);
                $post->setUpdatedAt($updatedAt);
            }

            // faire liker ce post par quelques Users aléatoires
            $nbLikers = mt_rand(0, count($users) - 1);
            for ($j = 0; $j < $nbLikers; $j++) {
                $randomIndex = mt_rand(0, count($users) - 1);
                $user = $users[$randomIndex];
                $user->like($post);
            }

            $manager->persist($post);
            array_push($posts, $post); // on n'oublie pas de le "ranger" dans la liste des posts crées !!
        }

        $manager->flush();
    }

    // Fonction qui s'assure que les UserFixtures sont chargées
    // avant de lancer les PostFixtures
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
