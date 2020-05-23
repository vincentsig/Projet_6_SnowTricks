<?php

namespace App\Tests\Form\Type;

use App\Entity\Video;
use App\Form\VideoType;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class VideoTypeTest extends TypeTestCase
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
            'url' => 'https://www.youtube.com/watch?v=AzJPhQdTRQQ',
        ];

        $objectToCompare = new Video();

        $expected = new Video();
        $expected->setUrl('https://www.youtube.com/embed/AzJPhQdTRQQ');

        // $formData will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(VideoType::class, $objectToCompare);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $objectToCompare);
    }
}
