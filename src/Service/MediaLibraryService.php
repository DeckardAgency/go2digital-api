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

    /**
     * Validate an uploaded file before reading its contents. Returns null when the file is
     * usable, otherwise ['message' => string, 'status' => int] suitable for a JSON response.
     *
     * Catches the case where PHP's upload limits were exceeded, which leaves the UploadedFile
     * with an empty temp path — subsequently calling getMimeType() on such a file throws
     * Symfony\Component\Mime\Exception\InvalidArgumentException ("The \"\" file does not exist...").
     */
    public static function validateUploadedFile(?UploadedFile $file): ?array
    {
        if (null === $file) {
            return ['message' => 'No file provided.', 'status' => 400];
        }

        if ($file->isValid()) {
            return null;
        }

        $code = $file->getError();
        $message = match ($code) {
            \UPLOAD_ERR_INI_SIZE => 'Uploaded file exceeds the server upload limit (upload_max_filesize).',
            \UPLOAD_ERR_FORM_SIZE => 'Uploaded file exceeds the form-defined size limit.',
            \UPLOAD_ERR_PARTIAL => 'The upload was interrupted; please retry.',
            \UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            \UPLOAD_ERR_NO_TMP_DIR => 'Server is missing a temporary directory for uploads.',
            \UPLOAD_ERR_CANT_WRITE => 'Server failed to write the uploaded file to disk.',
            \UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
            default => 'Upload failed.',
        };
        $status = \in_array($code, [\UPLOAD_ERR_INI_SIZE, \UPLOAD_ERR_FORM_SIZE], true) ? 413 : 400;

        return ['message' => $message, 'status' => $status];
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
