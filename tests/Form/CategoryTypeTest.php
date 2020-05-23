<?php

namespace App\Tests\Form\Type;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class CategoryTypeTest extends TypeTestCase
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
            'name' => 'Ollie',
            'description' => 'description'
        ];

        $objectToCompare = new Category();

        $expected = new Category();
        $expected->setName('Ollie');
        $expected->setDescription('description');





        // $formData will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(CategoryType::class, $objectToCompare);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $objectToCompare);
    }
}
