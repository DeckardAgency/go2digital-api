<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\HomepageAnalytics;
use App\Entity\HomepageBillboard;
use App\Entity\HomepageCustomImage;
use App\Entity\HomepageCustomSolution;
use App\Entity\HomepageHero;
use App\Entity\HomepageHumanFocused;
use App\Entity\HomepageRentalsImage;
use App\Entity\HomepageTextAnimation;
use App\Entity\HomepageWhySection;
use App\Entity\Setting;
use App\Service\MediaLibraryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TypographyController extends AbstractController
{
    private const ALLOWED_FONT_EXTENSIONS = ['woff2', 'woff', 'ttf', 'otf'];
    private const FORMAT_BY_EXTENSION = [
        'woff2' => 'woff2',
        'woff' => 'woff',
        'ttf' => 'truetype',
        'otf' => 'opentype',
    ];
    private const SINGLETON_CLASSES = [
        'homepage-hero' => HomepageHero::class,
        'homepage-billboard' => HomepageBillboard::class,
        'homepage-custom-image' => HomepageCustomImage::class,
        'homepage-custom-solution' => HomepageCustomSolution::class,
        'homepage-human-focused' => HomepageHumanFocused::class,
        'homepage-text-animation' => HomepageTextAnimation::class,
        'homepage-why-section' => HomepageWhySection::class,
        'homepage-analytics' => HomepageAnalytics::class,
        'homepage-rentals-image' => HomepageRentalsImage::class,
    ];

    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * Returns usage of each typography preset slug across singletons and block maps.
     * Shape: { "<slug>": { "count": int, "singletons": [...], "blockMaps": [...] } }
     */
    #[Route('/api/typography/usage', name: 'api_typography_usage', methods: ['GET'])]
    public function usage(): JsonResponse
    {
        $usage = [];

        foreach (self::SINGLETON_CLASSES as $type => $class) {
            $entity = $this->em->getRepository($class)->findOneBy([]);
            if (null === $entity || !method_exists($entity, 'getTypographyMap')) {
                continue;
            }
            $map = $entity->getTypographyMap() ?? [];
            foreach ($map as $slug) {
                if (!is_string($slug) || '' === $slug) {
                    continue;
                }
                $this->record($usage, $slug, 'singletons', $type);
            }
        }

        $blockMapSettings = $this->em->getRepository(Setting::class)
            ->createQueryBuilder('s')
            ->where('s.key LIKE :prefix')
            ->setParameter('prefix', 'typography.blockMaps.%')
            ->getQuery()
            ->getResult();

        foreach ($blockMapSettings as $setting) {
            $blockId = substr($setting->getKey(), strlen('typography.blockMaps.'));
            $map = $setting->getValue();
            if (!is_array($map)) {
                continue;
            }
            foreach ($map as $slug) {
                if (!is_string($slug) || '' === $slug) {
                    continue;
                }
                $this->record($usage, $slug, 'blockMaps', $blockId);
            }
        }

        return $this->json($usage);
    }

    private function record(array &$usage, string $slug, string $bucket, string $id): void
    {
        if (!isset($usage[$slug])) {
            $usage[$slug] = ['count' => 0, 'singletons' => [], 'blockMaps' => []];
        }
        $usage[$slug]['count']++;
        if (!in_array($id, $usage[$slug][$bucket], true)) {
            $usage[$slug][$bucket][] = $id;
        }
    }

    /**
     * Upload a font file (woff2/woff/ttf/otf) and return its public URL + format.
     * Admin then wires the returned { src, format } into a font weight entry in
     * the `typography.fonts` Setting.
     */
    #[Route('/api/typography/fonts/upload', name: 'api_typography_font_upload', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function uploadFont(Request $request, MediaLibraryService $mediaLibrary): JsonResponse
    {
        $file = $request->files->get('file');
        if (!$file) {
            return $this->json(['error' => 'No file provided'], Response::HTTP_BAD_REQUEST);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?? '');
        if (!in_array($ext, self::ALLOWED_FONT_EXTENSIONS, true)) {
            return $this->json([
                'error' => sprintf('Unsupported font format "%s". Allowed: %s', $ext, implode(', ', self::ALLOWED_FONT_EXTENSIONS)),
            ], Response::HTTP_BAD_REQUEST);
        }

        $media = $mediaLibrary->upload($file, 'fonts');

        return $this->json([
            'src' => '/storage/media/'.$media->getPath(),
            'format' => self::FORMAT_BY_EXTENSION[$ext] ?? $ext,
            'originalFilename' => $media->getOriginalFilename(),
            'size' => (int) $media->getSize(),
        ]);
    }
}
