<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

class MediaOptimizeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private string $mediaStoragePath,
    ) {
    }

    /**
     * Optimize an image: convert to WebP with quality setting.
     * POST /api/media/{id}/optimize
     */
    #[Route('/api/media/{id}/optimize', name: 'api_media_optimize', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function optimize(string $id, Request $request): JsonResponse
    {
        $media = $this->em->getRepository(Media::class)->find(Uuid::fromRfc4122($id));
        if (!$media) {
            throw $this->createNotFoundException('Media not found');
        }

        if (!$media->isImage()) {
            return $this->json(['error' => 'Only images can be optimized.'], 400);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        $quality = (int) ($data['quality'] ?? 80);
        $quality = max(1, min(100, $quality));

        $originalPath = $this->mediaStoragePath . '/' . $media->getPath();

        // Check if file exists, also try .webp variant (ThumbnailService may have already converted)
        if (!file_exists($originalPath)) {
            $webpVariant = preg_replace('/\.[^.]+$/', '.webp', $originalPath);
            if (file_exists($webpVariant)) {
                $originalPath = $webpVariant;
            } else {
                return $this->json(['error' => 'Original file not found.'], 404);
            }
        }

        $originalSize = filesize($originalPath);

        // If already WebP, just re-encode at the requested quality
        $manager = new ImageManager(new Driver());
        $image = $manager->read($originalPath);

        $webpPath = preg_replace('/\.[^.]+$/', '.webp', $originalPath);
        $webpRelative = preg_replace('/\.[^.]+$/', '.webp', $media->getPath());

        $image->toWebp(quality: $quality)->save($webpPath);
        $newSize = filesize($webpPath);

        // Remove old file if it was a different format
        if ($originalPath !== $webpPath && file_exists($originalPath)) {
            unlink($originalPath);
        }

        // Update entity
        $media->setPath($webpRelative);
        $media->setFilename(pathinfo($webpRelative, PATHINFO_BASENAME));
        $media->setMimeType('image/webp');
        $media->setSize((string) $newSize);

        // Update dimensions
        $info = $image->size();
        $media->setWidth($info->width());
        $media->setHeight($info->height());

        $this->em->flush();

        $savings = $originalSize - $newSize;
        $savingsPercent = $originalSize > 0 ? round(($savings / $originalSize) * 100, 1) : 0;

        return $this->json([
            'success' => true,
            'originalSize' => $originalSize,
            'optimizedSize' => $newSize,
            'savings' => $savings,
            'savingsPercent' => $savingsPercent,
            'mimeType' => 'image/webp',
            'path' => $webpRelative,
            'width' => $media->getWidth(),
            'height' => $media->getHeight(),
        ]);
    }
}
