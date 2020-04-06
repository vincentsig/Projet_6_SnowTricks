<?php

namespace App\Service;

use App\Entity\Profile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
