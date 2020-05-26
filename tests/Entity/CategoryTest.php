<?php

namespace Tests\Entity;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryTest extends KernelTestCase
{
    /*
    * Create a new EntityTest
    * @return Category
    */
    public function getEntity(): Category
    {
        return (new Category())
            ->setName('slide')
            ->setDescription('coucou');
    }
    /*
    * Count the number of error with the validator container
    * @param Category $video
    * @param int $number
    */
    public function assertHasErrors(Category $category, int $number = 0)
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($category);
        $this->assertCount($number, $error);
    }

    /*
    * Test if the entity is valid
    */
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /*
    * Set a name and an description to the Group
    */
    public function testvalidEntityCategory()
    {
        $this->assertHasErrors($this->getEntity()->setName('Jump'), 0);
        $this->assertHasErrors($this->getEntity()->setDescription('This is a description of the Group'), 0);
    }

    /*
    * A Group must have a name
    */
    public function testInvalidEntityCategoryBlank()
    {
        $this->assertHasErrors($this->getEntity()->setName(''), 1);
    }

    /*
    * A group must be unique
    */
    public function testInvalidEntityCategoryNotUnique()
    {
        $this->assertHasErrors($this->getEntity()->setName('One foot'), 1);
    }
}
