<?php

declare(strict_types=1);

namespace App\Service;

use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;

class ThumbnailService
{
    public const SIZES = [
        'small' => 400,
        'medium' => 800,
        'large' => 1400,
    ];

    private ImageManager $manager;

    public function __construct(
        private string $mediaStoragePath,
    ) {
        $this->manager = new ImageManager(new GdDriver());
    }

    /**
     * Generate thumbnails for an image file.
     *
     * @return array<string, string> Map of size name => relative path
     */
    public function generate(string $originalPath, string $baseName): array
    {
        $fullPath = $this->mediaStoragePath.'/'.$originalPath;

        if (!file_exists($fullPath)) {
            return [];
        }

        $thumbnails = [];
        $extension = 'webp';
        $nameWithoutExt = pathinfo($baseName, PATHINFO_FILENAME);

        foreach (self::SIZES as $sizeName => $width) {
            $thumbFilename = sprintf('%s_%s.%s', $nameWithoutExt, $sizeName, $extension);
            $thumbRelativePath = 'thumbnails/'.$thumbFilename;
            $thumbFullPath = $this->mediaStoragePath.'/'.$thumbRelativePath;

            $image = $this->manager->read($fullPath);
            $image->scaleDown(width: $width);
            $image->toWebp(quality: 80)->save($thumbFullPath);

            $thumbnails[$sizeName] = $thumbRelativePath;
        }

        return $thumbnails;
    }

    /**
     * Delete thumbnails for a media item.
     */
    public function deleteThumbnails(?array $thumbnails): void
    {
        if (null === $thumbnails) {
            return;
        }

        foreach ($thumbnails as $path) {
            $fullPath = $this->mediaStoragePath.'/'.$path;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
}
