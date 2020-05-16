<?php

namespace Tests\Entity;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class CategoryTest extends KernelTestCase
{

    public function getEntity(): Category
    {
        return (new Category())
            ->setName('Ollie')
            ->setDescription('coucou');
    }

    public function assertHasErrors(Category $category, int $number = 0)
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($category);
        $this->assertCount($number, $error);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testvalidEntityCategory()
    {
        $this->assertHasErrors($this->getEntity()->setName('Jump'), 0);
        $this->assertHasErrors($this->getEntity()->setDescription('azeazeazaeza'), 0);
    }

    public function testInvalidEntityCategoryBlank()
    {
        $this->assertHasErrors($this->getEntity()->setName(''), 1);
    }

    public function testInvalidEntityCategoryNotUnique()
    {
        $this->assertHasErrors($this->getEntity()->setName('Slide'), 1);
    }
}
