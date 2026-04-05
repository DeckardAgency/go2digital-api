<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\HomepageHero;
use App\Service\MediaLibraryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomepageHeroController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('/api/homepage-hero/video/{type}', name: 'api_homepage_hero_video_upload', methods: ['POST'], requirements: ['type' => 'desktop|mobile'])]
    #[IsGranted('ROLE_EDITOR')]
    public function uploadVideo(string $type, Request $request, MediaLibraryService $mediaLibrary): JsonResponse
    {
        $hero = $this->em->getRepository(HomepageHero::class)->findOneBy([]);
        if (!$hero) {
            return $this->json(['error' => 'Homepage hero not found. Run fixtures.'], Response::HTTP_NOT_FOUND);
        }

        $file = $request->files->get('video');
        if (!$file) {
            return $this->json(['error' => 'No video file provided'], Response::HTTP_BAD_REQUEST);
        }

        $media = $mediaLibrary->upload($file, 'homepage');

        if ($type === 'desktop') {
            $hero->setVideo($media);
        } else {
            $hero->setMobileVideo($media);
        }

        $this->em->flush();

        return $this->json([
            'success' => true,
            'type' => $type,
            'mediaId' => $media->getId()->toRfc4122(),
            'videoUrl' => '/storage/media/' . $media->getPath(),
            'originalFilename' => $media->getOriginalFilename(),
        ]);
    }

    #[Route('/api/homepage-hero/video/{type}', name: 'api_homepage_hero_video_remove', methods: ['DELETE'], requirements: ['type' => 'desktop|mobile'])]
    #[IsGranted('ROLE_EDITOR')]
    public function removeVideo(string $type): JsonResponse
    {
        $hero = $this->em->getRepository(HomepageHero::class)->findOneBy([]);
        if (!$hero) {
            return $this->json(['error' => 'Homepage hero not found'], Response::HTTP_NOT_FOUND);
        }

        if ($type === 'desktop') {
            $hero->setVideo(null);
        } else {
            $hero->setMobileVideo(null);
        }

        $this->em->flush();

        return $this->json(['success' => true]);
    }
}
