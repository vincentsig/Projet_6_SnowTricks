<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Profile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
{
    private $targetDirectoryTrick;
    private $targetDirectoryAvatar;
    private $targetDirectory;

    public function __construct($targetDirectoryTrick, $targetDirectoryAvatar)
    {
        $this->targetDirectoryTrick = $targetDirectoryTrick;
        $this->targetDirectoryAvatar = $targetDirectoryAvatar;
    }

    public function upload(UploadedFile $file, $entity)
    {
        if ($entity instanceof Profile) {
            $this->targetDirectory = $this->targetDirectoryAvatar;
        } else {
            $this->targetDirectory = $this->targetDirectoryTrick;
        }

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        try {
            $file->move($this->targetDirectory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        return $fileName;
    }

    public function removeFile($entity, $filename)
    {
        if ($entity instanceof Profile) {
            $this->targetDirectory = $this->targetDirectoryAvatar;
        } else {
            $this->targetDirectory = $this->targetDirectoryTrick;
        }
        $filesystem = new Filesystem();
        $filesystem->remove($this->getTargetDirectory() . '/' . $filename);
    }

    public function removeAllFiles($filesname)
    {
        $this->targetDirectory = $this->targetDirectoryTrick;
        $filesystem = new Filesystem();
        foreach ($filesname as $file) {
            $filename = $file->getFilename();
            $filesystem->remove($this->getTargetDirectory() . '/' . $filename);
        }
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
