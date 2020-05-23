<?php

namespace Tests\Entity;

use App\Entity\Profile;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfileTest extends KernelTestCase
{
    /*
    * Create a new EntityTest
    * @return Profile
    */
    public function getEntity(): Profile
    {
        return (new Profile())
            ->setFirstname('firstname')
            ->setLastname('lastname')
            ->setPresentation('This is my presentation');
    }

    /*
    * Count the number of error with the validator container
    * @param Category $profile
    * @param int $number
    */
    public function assertHasErrors(Profile $profile, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($profile);
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . '=>' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    /*
    * Test if the entity is valid
    */
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /*
    * Test if we get an avatarFilename by default when we instance a new Profile Object
    */
    public function testDefaultAvatar()
    {
        $this->assertSame('default-profile.png', $this->getEntity()->getAvatarFilename());
    }
}
