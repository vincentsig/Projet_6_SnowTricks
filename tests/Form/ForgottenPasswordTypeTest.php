<?php

namespace App\Tests\Form\Type;


use App\Entity\User;
use App\Form\ForgottenPasswordType;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class ForgottenPasswordTypeTest extends TypeTestCase
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
            'email' => [
                'first' => 'email@email.com ',
                'second' => 'email@email.com'
            ]
        ];

        $objectToCompare = new User();

        $expected = new User();
        $expected->setEmail('email@email.com');

        // $formData will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(ForgottenPasswordType::class, $objectToCompare);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $objectToCompare);
    }
}
