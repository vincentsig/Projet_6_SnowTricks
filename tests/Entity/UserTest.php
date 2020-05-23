<?php

namespace Tests\Entity;

use App\Entity\User;
use App\Entity\Profile;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    protected $profile;

    public function setUp(): void
    {
        $this->profile = new Profile();
    }

    /*
    * Create a new EntityTest
    * @return User
    */
    public function getEntity(): User
    {
        return (new User())
            ->setUsername('username')
            ->setRoles(['ROLE_USER'])
            ->setPassword('password')
            ->setEmail('email@email.com')
            ->setCreatedAt(null)
            ->setProfile($this->profile);
    }

    /*
    * Count the number of error with the validator container
    * @param Category $user
    * @param int $number
    */
    public function assertHasErrors(User $user, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($user);
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

    /**
     * Test the constraints NotBlank and Length min=8
     */
    public function testInvalidEntityUserName()
    {
        $this->assertHasErrors($this->getEntity()->setUsername(''), 2);
    }

    /*
    * The email must be a emailType
    */
    public function testvaliEmailType()
    {
        $this->assertHasErrors($this->getEntity()->setEmail('validemail@email.com'), 0);
        $this->assertHasErrors($this->getEntity()->setEmail('This is not an email'), 1);
    }

    /*
    * Set ConfirmationToken
    */
    public function testSettingConfirmationToken()
    {
        $this->assertHasErrors($this->getEntity()
            ->setConfirmationToken('hVF4CVDlbuUg18MmRZBA4pDkzuXZi9Rzm5wYvSPtxvF8qa8CK9GiJqMXdAMv'), 0);
    }
}
