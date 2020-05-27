<?php

namespace Tests\Entity;

use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VideoTest extends KernelTestCase
{
    /*
    * Create a new EntityTest
    * @return Video
    */
    public function getEntity(): Video
    {
        return (new Video())
            ->setUrl('https://www.youtube.com/watch?v=AzJPhQdgTRQQ');
    }

    /*
    * Count the number of error with the validator container
    * @param Category $video
    * @param int $number
    */
    public function assertHasErrors(Video $video, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($video);
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . '=>' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    /*
    * Test if the entity is valid
    */
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /*
    * Test if it's a valid Daylimotion video
    */
    public function testvalidEntityVideoDaylimotion()
    {
        $this->assertHasErrors($this->getEntity()->setUrl('https://www.dailymotion.com/video/x6bq2cb'), 0);
    }

    /*
    * Test if it's a valid Youtube video
    */
    public function testvalidEntityYoutube()
    {
        $this->assertHasErrors($this->getEntity()->setUrl('https://www.youtube.com/watch?v=n0F6hSpxaFc'), 0);
    }

    /*
    * Test Invalid Random Url
    */
    public function testInvalidRandomUrl()
    {
        $this->assertHasErrors($this->getEntity()->setUrl('https://openclassrooms.com/fr'), 1);
    }

    /*
    * The Url must be a video link from youtube or daylimotion
    */
    public function testInvalidEntityVideoUrl()
    {
        $this->assertHasErrors($this->getEntity()->setUrl('this is not a daylimotion or youtube URL'), 1);
    }

    /*
    * The Url can be null
    */
    public function testvalidEntityVideoNull()
    {
        $this->assertHasErrors($this->getEntity()->setUrl(''), 0);
    }

    /*
    * Slug the url Set into a Embed url in the Database
    */
    public function testSluggerUrl()
    {
        $video = new Video();
        $video->setUrl('https://www.youtube.com/watch?v=AzJPhQdTRQQ');

        $this->assertSame($video->getUrl(), 'https://www.youtube.com/embed/AzJPhQdTRQQ');
    }

    /*
    * Slug the iframe to only get the embed url Database
    */
    public function testSluggerWithEmbedUrl()
    {
        $video = new Video();
        $video->setUrl('<iframe width="560" height="315" src="https://www.youtube.com/embed/AzJPhQdTRQQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');

        $this->assertSame($video->getUrl(), 'https://www.youtube.com/embed/AzJPhQdTRQQ');
    }
}
