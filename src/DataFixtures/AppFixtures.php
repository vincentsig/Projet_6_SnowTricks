<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Profile;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $em)
    {
        $faker = \Faker\Factory::create('FR-fr');

        $users = [];
        $profiles = [];
        $categories = [];
        $videoDemo = [
            'https://www.youtube.com/watch?v=64LmGX0LTpQ',
            'https://www.youtube.com/watch?v=AMMzY4TT3i4',
            'https://www.youtube.com/watch?v=UGdif-dwu-8&t=536s',
            'https://www.dailymotion.com/video/x1ey4wl',
            'https://www.youtube.com/watch?v=o7OB24ACnVM'
        ];
        $categoriesDemoName = ['Grabs', 'Rotations', 'Flips', 'Rotations désaxées', 'Slides', 'One foot', 'Old school'];
        $tricksDemoName = ['Mute', 'Indy', '360', '720', 'Backflip', 'Misty', 'Tail slide', 'Method air', 'Backside air'];
        $imageFileNames = ['default.jpg', 'fixture-1.jpg', 'fixture-2.jpg', 'fixture-3.jpg', 'fixture-4.jpg', 'fixture-5.jpg', 'fixture-6.jpg'];
        $avatarFilenames = ['fixture-1.jpg', 'fixture-2.jpg', 'fixture-3.jpg', 'fixture-4.jpg',];

        // Create Profile
        for ($i = 0; $i < 5; $i++) {
            $profile = new Profile();
            $profile->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setPresentation($faker->paragraph(5))
                ->setAvatarFileName($faker->randomElement($avatarFilenames));

            $em->persist($profile);
            $profiles[] = $profile;
        }
        // Create User depending on Profile
        foreach ($profiles as $key => $profile) {
            $user = new User();
            $user->setUsername('username-' . $key)
                ->setEmail($faker->safeEmail)
                ->setPassword($this->encoder->encodePassword($user, 'password'))
                ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                ->setProfile($profile);

            $em->persist($user);
            $users[] = $user;
        }

        // Categories
        foreach ($categoriesDemoName as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);

            $em->persist($category);
            $categories[] = $category;
        }

        //Tricks
        foreach ($tricksDemoName as $trickName) {
            $trick = new Trick();
            $trick->setName($trickName)
                ->setDescription($faker->paragraph(5))
                ->setCreatedAt(new \Datetime)
                ->setCategory($faker->randomElement($categories));

            //Images
            for ($k = 1; $k < 5; $k++) {
                $image = new Image();
                $image->setFilename($faker->randomElement($imageFileNames))
                    ->setTrick($trick);

                $em->persist($image);
            }
            // Videos
            for ($l = 0; $l < mt_rand(1, 4); $l++) {
                $video = new Video();
                $video->setUrl($faker->randomElement($videoDemo))
                    ->setTrick($trick);

                $em->persist($video);
            }

            // Comments
            for ($m = 0; $m < mt_rand(0, 30); $m++) {
                $comment = new Comment();
                $comment->setContent($faker->sentence(mt_rand(1, 5)))
                    ->setCreatedAt(new \Datetime)
                    ->setAuthor($faker->randomElement($users))
                    ->setTrick($trick);

                $em->persist($comment);
            }
            $em->persist($trick);
        }

        $em->flush();
    }
}
