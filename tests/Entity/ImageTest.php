<?php

namespace Tests\Entity;

use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageTest extends KernelTestCase
{
    public function setUp(): void
    {
        $this->file = new UploadedFile(
            dirname((__DIR__)) . '/fixtures/default.jpg',
            'photo.jpg',
            'image/jpeg',
            null,
            true
        );
    }

    /*
    * Create a new EntityTest
    * @return Image
    */
    public function getEntity()
    {
        return (new Image())
            ->setFilename('default.jpg')
            ->setFile($this->file);
    }

    /*
    * Count the number of error with the validator container
    * @param Category $image
    * @param int $number
    */
    public function assertHasErrors(Image $image, int $number = 0)
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($image);
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
    * The $file must be an instance of UploadedFile
    */
    public function testInstanceOfUploadeFile()
    {
        $this->assertInstanceOf(UploadedFile::class, $this->getEntity()->getFile());
    }

    /*
    * The File can't be null
    */
    public function testInvalidNullOnFile()
    {
        $this->assertHasErrors($this->getEntity()->setFile(null), 1);
    }
}
