<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Media;
use App\Entity\User;
use App\Message\GenerateThumbnailsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class MediaLibraryService
{
    public function __construct(
        private string $mediaStoragePath,
        private EntityManagerInterface $em,
        private MessageBusInterface $messageBus,
        private ThumbnailService $thumbnailService,
    ) {
    }

    public function upload(UploadedFile $file, string $collection, ?User $user = null): Media
    {
        $originalFilename = $file->getClientOriginalName();
        $mimeType = $file->getMimeType() ?? $file->getClientMimeType();
        $size = $file->getSize();
        $extension = $file->guessExtension() ?? $file->getClientOriginalExtension();
        $filename = Uuid::v4()->toRfc4122().'.'.$extension;
        $relativePath = 'originals/'.$filename;

        // Move file to storage
        $file->move($this->mediaStoragePath.'/originals', $filename);

        // Get dimensions for images
        $width = null;
        $height = null;
        $duration = null;

        if (str_starts_with($mimeType, 'image/')) {
            $fullPath = $this->mediaStoragePath.'/'.$relativePath;
            $imageSize = @getimagesize($fullPath);
            if (false !== $imageSize) {
                $width = $imageSize[0];
                $height = $imageSize[1];
            }
        }

        // Create Media entity
        $media = new Media();
        $media->setFilename($filename);
        $media->setOriginalFilename($originalFilename);
        $media->setMimeType($mimeType);
        $media->setSize((string) $size);
        $media->setWidth($width);
        $media->setHeight($height);
        $media->setDuration($duration);
        $media->setCollection($collection);
        $media->setDisk('local');
        $media->setPath($relativePath);
        $media->setUploadedBy($user);

        $this->em->persist($media);
        $this->em->flush();

        // Dispatch async thumbnail generation for images
        if ($media->isImage()) {
            $this->messageBus->dispatch(new GenerateThumbnailsMessage($media->getId()->toRfc4122()));
        }

        return $media;
    }

    public function delete(Media $media): void
    {
        // Delete original file
        $fullPath = $this->mediaStoragePath.'/'.$media->getPath();
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        // Delete thumbnails
        $this->thumbnailService->deleteThumbnails($media->getThumbnails());

        $this->em->remove($media);
        $this->em->flush();
    }

    public function getUrl(Media $media, ?string $size = null): string
    {
        if (null !== $size && null !== $media->getThumbnails()) {
            $thumbnails = $media->getThumbnails();
            if (isset($thumbnails[$size])) {
                return '/storage/media/'.$thumbnails[$size];
            }
        }

        return '/storage/media/'.$media->getPath();
    }
}
