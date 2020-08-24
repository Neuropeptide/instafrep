<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class GroupFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        $users = $manager->getRepository(User::class)->findAll();
        $nbGroup = 10;
        $groups = [];

        for ($i=0; $i <= $nbGroup ; $i++){
            $group = new Group();
            // auteur aléatoire
            /** @var int $randomIndex */
            $randomIndex = array_rand($users);

            /** @var User $creator */
            $creator = $users[$randomIndex];

            $group->setCreator($creator)
                ->setName($faker->realText(50))
                ->setDescription($faker->realText(200))
            ->setCreatedAt($faker->dateTimeThisYear())
            ->addMember($creator);

            //on ajoute des membres
            $nbMembers = mt_rand(1,5);
            for ($j=0; $j<$nbMembers;$j++){
                /** @var int $randomIndexMembers */
                $randomIndexMembers = array_rand($users);
                /** @var User $member */
                $member = $users[$randomIndexMembers];
                $group->addMember($member);
            }

            $manager->persist($group);
            array_push($groups, $group);

        }

        $manager->flush();

        //maintenant on ajoute des postes au groupe

        $groupList = $manager->getRepository(Group::class)->findAll();
        foreach ($groupList as $groupSingle ){
            $nbPostGroup = mt_rand(1,8);
            for ($k=0; $k<$nbPostGroup; $k++){

                $createdAt = $faker->dateTimeThisYear;
                $randomIndex = array_rand($users);
                $author = $users[$randomIndex];
                /** @var User $author */

                $groupPost = new Post;
                /** @var Group $groupSingle */
                $groupPost->setAuthor($author)
                    ->setTitle($faker->words(rand(4, 5), true))
                    ->setContent($faker->realText())
                    ->setCreatedAt($createdAt)
                    ->setViews(0)
                    ->setPapaGroup($groupSingle);

                $manager->persist($groupPost);

            }
            $manager->flush();

        }

    }

    // Fonction qui s'assure que les UserFixtures sont chargées
    // avant de lancer les PostFixtures

    public function getDependencies()
    {
        return [
            PostFixtures::class,
        ];
    }
}
