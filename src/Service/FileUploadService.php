<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\File;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class FileUploadService
{
    public function __construct(
        private string $filesStoragePath,
        private EntityManagerInterface $em,
    ) {
    }

    public function upload(UploadedFile $file, string $category, ?string $description = null, ?User $user = null): File
    {
        $originalFilename = $file->getClientOriginalName();
        $mimeType = $file->getMimeType() ?? $file->getClientMimeType();
        $size = $file->getSize();
        $extension = $file->guessExtension() ?? $file->getClientOriginalExtension();
        $filename = Uuid::v4()->toRfc4122().'.'.$extension;

        $file->move($this->filesStoragePath, $filename);

        $fileEntity = new File();
        $fileEntity->setFilename($filename);
        $fileEntity->setOriginalFilename($originalFilename);
        $fileEntity->setMimeType($mimeType);
        $fileEntity->setSize((string) $size);
        $fileEntity->setDisk('local');
        $fileEntity->setPath($filename);
        $fileEntity->setCategory($category);
        $fileEntity->setDescription($description);
        $fileEntity->setUploadedBy($user);

        $this->em->persist($fileEntity);
        $this->em->flush();

        return $fileEntity;
    }

    public function delete(File $file): void
    {
        $fullPath = $this->filesStoragePath.'/'.$file->getPath();
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $this->em->remove($file);
        $this->em->flush();
    }

    public function getFullPath(File $file): string
    {
        return $this->filesStoragePath.'/'.$file->getPath();
    }
}
