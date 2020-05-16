<?php

namespace Tests\Entity;

use App\Entity\User;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class CommentTest extends KernelTestCase
{
    /*
    * Create a new EntityTest
    * @return Comment
    */
    public function getEntity(): Comment
    {
        $user = new User();
        $user->setUsername('coco');
        return (new Comment())
            ->setAuthor($user)
            ->setContent('This is a test comment')
            ->setCreatedAt(new \Datetime());
    }

    /*
    * Count the number of error with the validator container
    * @param Category $comment
    * @param int $number
    */
    public function assertHasErrors(Comment $comment, int $number = 0)
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($comment);
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
    * The Content must be a string 
    */
    public function testvalidEntityComment()
    {
        $this->assertHasErrors($this->getEntity()->setContent('This comment is a string'), 0);
    }

    /*
    * The Content can't be null
    */
    public function testInvalidEntityCommentNull()
    {
        $this->assertHasErrors($this->getEntity()->setContent(''), 1);
    }
}
