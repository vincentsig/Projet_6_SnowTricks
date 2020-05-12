<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
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
    private $filename;

    /*
     * 
     * @Assert\NotBlank(message="Veuillez ajouter une photo.")
     * @Assert\Image(
     * mimeTypes={"image/jpeg", "image/png", "image/jpg"},
     * mimeTypesMessage="L'image doit avoir une extension .jpeg .jpg ou .png",
     * allowPortrait = false,
     * allowPortraitMessage = " Les images en portrait ne sont pas acceptés,veuillez choisir un format paysage"
     * ) 
     * 
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="imageList")
     */
    private $trick;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
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

    /**
     * Get the value of filename
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set the value of filename
     *
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }
}
