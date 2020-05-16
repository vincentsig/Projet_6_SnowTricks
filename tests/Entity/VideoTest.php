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
            ->setUrl('https://www.youtube.com/watch?v=AzJPhQdTRQQ');
    }

    /*
    * Count the number of error with the validator container
    * @param Category $video
    * @param int $number
    */
    public function assertHasErrors(Video $video, int $number = 0)
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($video);
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
    * 
    */
    public function testvalidEntityVideoDaylimotion()
    {
        $this->assertHasErrors($this->getEntity()->setUrl('https://www.dailymotion.com/video/x6bq2cb'), 0);
    }

    /*
    * The Url must be a video link from youtube or daylimotion
    */
    public function testvalidEntityVideoUrl()
    {
        $this->assertHasErrors($this->getEntity()->setUrl('this is not a url daylimotion or youtube'), 1);
    }
    /*
    * The Url can be null
    */
    public function testvalidEntityVideoNull()
    {
        $this->assertHasErrors($this->getEntity()->setUrl(''), 0);
    }
}
