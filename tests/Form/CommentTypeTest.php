<?php

namespace App\Tests\Form\Type;

use DateTime;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Constraints\Date;

class CommentTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        $validator = Validation::createValidator();

        // or if you also need to read constraints from annotations
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }

    public function testSubmitValidData()
    {
        $user = $this->createMock(User::class);
        $date = $this->createMock(DateTime::class);

        $formData = [
            'content' => 'content',
            'author' => $user,
            'trick' => null,
            'createdAt' => $date
        ];

        $objectToCompare = new Comment();

        $expected = new Comment();
        $expected->setContent('content');
        $expected->setAuthor($user);
        $expected->setTrick(null);
        $expected->setCreatedAt($date);

        // $formData will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(CommentType::class, $objectToCompare);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $objectToCompare);
    }
}
