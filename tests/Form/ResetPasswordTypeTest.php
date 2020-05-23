<?php

namespace App\Tests\Form\Type;

use App\Entity\User;
use App\Form\ResetPasswordType;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class ResetPasswordTypeTest extends TypeTestCase
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
        $formData = [
            'password' => [
                'first' => 'password',
                'second' => 'password'
            ]
        ];

        $objectToCompare = new User();

        $expected = new User();
        $expected->setPassword('password');

        // $formData will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(ResetPasswordType::class, $objectToCompare);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $objectToCompare);
    }
}
