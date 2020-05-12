<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 */
class Video
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="videos")
     */
    private $trick;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        //check if it's a youtube or daylimotion video
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $youtubeMatch);
        preg_match('%(?:https?://)?(?:www\.)?(?:dai\.ly/|dailymotion\.com(?:/video/|/embed/|/embed/video/))([^^&?/ ]{7})%i', $url, $dailymotionMatch);
        // change the url to an embed url to be integrated properly in the template
        if (!empty($youtubeMatch)) {
            $url = "https://www.youtube.com/embed/$youtubeMatch[1]";
        } elseif (!empty($dailymotionMatch)) {
            $url = "https://www.dailymotion.com/embed/video/$dailymotionMatch[1]";
        }
        $this->url = $url;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }
}
