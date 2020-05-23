<?php

namespace Tests\Entity;

use App\Entity\Trick;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TrickTest extends KernelTestCase
{

    /*
    * Create a new EntityTest
    * @return Trick
    */
    public function getEntity(): Trick
    {
        return (new Trick())
            ->setName('Ollie 360')
            ->setDescription('this is a description of the Trick')
            ->setCreatedAt(new \DateTime('NOW'))
            ->setCategory(new Category);
    }

    /*
    * Count the number of error with the validator container
    * @param Category $user
    * @param int $number
    */
    public function assertHasErrors(Trick $user, int $number = 0)
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

    /*
    * The name can't be null
    */
    public function testName()
    {
        $this->assertHasErrors($this->getEntity()->setName(''), 1);
    }


    /*
    * Test the slugger during the setting of nameTrick
    */
    public function testSlug()
    {
        $this->assertSame('ollie-360', $this->getEntity()->getSlug());
    }
}
