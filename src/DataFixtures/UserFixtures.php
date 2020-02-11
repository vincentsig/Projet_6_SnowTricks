<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;
 
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
 
    public function load(ObjectManager $manager)
    {
        // choose the language for the data
        $faker = Faker\Factory::create('fr_FR');
 
        // create 10 users fixtures
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($faker->name);
            $user->setEmail(sprintf('userdemo%d@example.com', $i));
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'userdemo'
            ));
            $user->setCreatedAt(new \DateTime());
            
            $manager->persist($user);
        }
 
        $manager->flush();
 
    }
}
