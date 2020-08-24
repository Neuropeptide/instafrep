<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    /**
     * UserFixtures constructor.
     * @param $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');
        $plainPassword = 'azeaze';

        $me = $manager->getRepository(User::class)->findOneBy(['username' => 'Pierre']);
        if ($me === null) {
            $me = new User();
            $hash = $this->encoder->encodePassword($me, $plainPassword);
            $me->setUsername('Pierre')
                ->setPassword($hash)
                ->setFullName('Pierre Guillaume')
                ->setRoles(['ROLE_VERIFIED'])
                ->setBirthday(new \DateTime("1988-08-18"));

            $manager->persist($me);
        }
        $clementine = $manager->getRepository(User::class)->findOneBy(['username' => 'Clementine']);
        if ($clementine === null) {
            $clementine = new User();
            $hash = $this->encoder->encodePassword($clementine, $plainPassword);
            $clementine->setUsername('Clementine')
                ->setPassword($hash)
                ->setFullName('Clementine Mas')
                ->setRoles(['ROLE_VERIFIED'])
                ->setBirthday(new \DateTime("1997-07-31"));

            $manager->persist($clementine);
        }


        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $hash = $this->encoder->encodePassword($user, $plainPassword);

            $user->setUsername($faker->unique()->userName)
                ->setPassword($hash)
                ->setFullName($faker->unique()->name)
                ->setBirthday($faker->dateTimeBetween('-80 years', '-18 years'))
                ->setRoles(['ROLE_VERIFIED']);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
