<?php

namespace App\Tests\Form\Type;

use App\Entity\Profile;
use App\Form\AccountType;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class AccountTypeTest extends TypeTestCase
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
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'presentation' => 'presentation',
            'avatar' => null
        ];

        $objectToCompare = new Profile();

        $expected = new Profile();
        $expected->setFirstname('firstname');
        $expected->setLastname('lastname');
        $expected->setPresentation('presentation');
        $expected->setAvatar(null);

        // $formData will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(AccountType::class, $objectToCompare);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $objectToCompare);
    }
}
