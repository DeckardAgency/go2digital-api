<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Serves media files in development. In production, use Nginx/CDN to serve /storage/media/ directly.
 */
class MediaServeController extends AbstractController
{
    #[Route('/storage/media/{path}', name: 'media_serve', requirements: ['path' => '.+'], methods: ['GET'])]
    public function serve(string $path): Response
    {
        $fullPath = $this->getParameter('app.media_storage_path').'/'.$path;

        if (!file_exists($fullPath)) {
            throw $this->createNotFoundException('Media file not found.');
        }

        return new BinaryFileResponse($fullPath);
    }
}
