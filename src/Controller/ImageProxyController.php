<?php

declare(strict_types=1);

namespace App\Controller;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImageProxyController extends AbstractController
{
    private const CDN_BASE = 'https://cdn.go2digital.hr';

    public function __construct(
        private HttpClientInterface $httpClient,
        private KernelInterface $kernel,
    ) {
    }

    /**
     * Proxy a CDN image and serve as WebP.
     * GET /api/image-proxy?url=/media/CACHE/...&q=80
     */
    #[Route('/api/image-proxy', name: 'api_image_proxy', methods: ['GET'])]
    public function proxy(Request $request): Response
    {
        $url = $request->query->get('url', '');
        $quality = min(100, max(1, (int) $request->query->get('q', 80)));

        // Validate: only allow CDN media paths
        if (!str_starts_with($url, '/media/')) {
            throw $this->createNotFoundException('Invalid image path');
        }

        // Build cache path
        $cacheDir = $this->kernel->getProjectDir() . '/var/cache/webp';
        $cacheKey = md5($url . ':' . $quality) . '.webp';
        $cachePath = $cacheDir . '/' . $cacheKey;

        // Serve from cache if exists
        if (file_exists($cachePath)) {
            return $this->serveWebp($cachePath);
        }

        // Fetch from CDN
        try {
            $response = $this->httpClient->request('GET', self::CDN_BASE . $url);
            $imageData = $response->getContent();
        } catch (\Throwable $e) {
            throw $this->createNotFoundException('Image not found on CDN');
        }

        // Convert to WebP
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($imageData);
            $webpData = $image->toWebp($quality)->toString();
        } catch (\Throwable $e) {
            // If conversion fails, serve original with correct headers
            return new Response($imageData, 200, [
                'Content-Type' => $response->getHeaders()['content-type'][0] ?? 'image/jpeg',
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }

        // Cache to disk
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0775, true);
        }
        file_put_contents($cachePath, $webpData);

        return $this->serveWebp($cachePath);
    }

    private function serveWebp(string $path): BinaryFileResponse
    {
        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', 'image/webp');
        $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        $response->setPublic();
        $response->setMaxAge(31536000);

        return $response;
    }
}
