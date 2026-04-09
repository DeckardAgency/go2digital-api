<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogPageContent;
use App\Entity\BlogPost;
use App\Entity\ContactPageContent;
use App\Entity\EsgPageContent;
use App\Entity\LabPageContent;
use App\Entity\LabProject;
use App\Entity\Page;
use App\Entity\SeoMetadata;
use App\Entity\TeamPageContent;
use App\Entity\Totem;
use App\Entity\Translation\SeoMetadataTranslation;
use App\Service\SeoGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

/**
 * Generic SEO metadata controller.
 * Works with any entity that has a getSeoMetadata()/setSeoMetadata() method.
 */
class SeoController extends AbstractController
{
    private const ENTITY_MAP = [
        'blog-posts' => BlogPost::class,
        'lab-projects' => LabProject::class,
        'pages' => Page::class,
        'totems' => Totem::class,
    ];

    public function __construct(
        private EntityManagerInterface $em,
        private SeoGeneratorService $seoGenerator,
    ) {
    }

    /**
     * Get SEO metadata for an entity.
     * GET /api/seo/{entityType}/{entityId}
     */
    #[Route('/api/seo/{entityType}/{entityId}', name: 'api_seo_get', methods: ['GET'])]
    #[IsGranted('ROLE_EDITOR')]
    public function get(string $entityType, string $entityId): JsonResponse
    {
        $entity = $this->findEntity($entityType, $entityId);
        $seo = $entity->getSeoMetadata();

        if (!$seo) {
            return $this->json([
                'exists' => false,
                'translations' => ['hr' => $this->emptyTranslation(), 'en' => $this->emptyTranslation()],
                'ogType' => 'website',
                'twitterCard' => 'summary_large_image',
                'canonicalUrl' => null,
                'noIndex' => false,
                'noFollow' => false,
            ]);
        }

        return $this->json([
            'exists' => true,
            'id' => $seo->getId()->toRfc4122(),
            'translations' => [
                'hr' => $this->serializeTranslation($seo->translate('hr')),
                'en' => $this->serializeTranslation($seo->translate('en')),
            ],
            'ogType' => $seo->getOgType(),
            'twitterCard' => $seo->getTwitterCard(),
            'canonicalUrl' => $seo->getCanonicalUrl(),
            'noIndex' => $seo->isNoIndex(),
            'noFollow' => $seo->isNoFollow(),
            'ogImageUrl' => $seo->getOgImage() ? '/storage/media/' . $seo->getOgImage()->getPath() : null,
        ]);
    }

    /**
     * Save SEO metadata for an entity.
     * PUT /api/seo/{entityType}/{entityId}
     */
    #[Route('/api/seo/{entityType}/{entityId}', name: 'api_seo_save', methods: ['PUT'])]
    #[IsGranted('ROLE_EDITOR')]
    public function save(string $entityType, string $entityId, Request $request): JsonResponse
    {
        $entity = $this->findEntity($entityType, $entityId);
        $data = json_decode($request->getContent(), true);

        $seo = $entity->getSeoMetadata();
        if (!$seo) {
            $seo = new SeoMetadata();
            $this->em->persist($seo);
            $entity->setSeoMetadata($seo);
        }

        // Non-translatable fields
        if (array_key_exists('ogType', $data)) $seo->setOgType($data['ogType']);
        if (array_key_exists('twitterCard', $data)) $seo->setTwitterCard($data['twitterCard']);
        if (array_key_exists('canonicalUrl', $data)) $seo->setCanonicalUrl($data['canonicalUrl']);
        if (array_key_exists('noIndex', $data)) $seo->setNoIndex($data['noIndex']);
        if (array_key_exists('noFollow', $data)) $seo->setNoFollow($data['noFollow']);

        // Translations
        if (isset($data['translations'])) {
            foreach ($data['translations'] as $locale => $fields) {
                $translation = $seo->translate($locale);
                if (!$translation) {
                    $translation = new SeoMetadataTranslation();
                    $translation->setLocale($locale);
                    $seo->addTranslation($translation);
                }

                if (array_key_exists('title', $fields)) $translation->setTitle($fields['title']);
                if (array_key_exists('description', $fields)) $translation->setDescription($fields['description']);
                if (array_key_exists('keywords', $fields)) $translation->setKeywords($fields['keywords']);
                if (array_key_exists('ogTitle', $fields)) $translation->setOgTitle($fields['ogTitle']);
                if (array_key_exists('ogDescription', $fields)) $translation->setOgDescription($fields['ogDescription']);
                if (array_key_exists('twitterTitle', $fields)) $translation->setTwitterTitle($fields['twitterTitle']);
                if (array_key_exists('twitterDescription', $fields)) $translation->setTwitterDescription($fields['twitterDescription']);
            }
        }

        $this->em->flush();

        return $this->json(['success' => true, 'id' => $seo->getId()->toRfc4122()]);
    }

    private const SINGLETON_MAP = [
        'blog-page' => BlogPageContent::class,
        'lab-page' => LabPageContent::class,
        'esg-page' => EsgPageContent::class,
        'contact-page' => ContactPageContent::class,
        'team-page' => TeamPageContent::class,
    ];

    /**
     * Get SEO for a singleton page.
     */
    #[Route('/api/seo/singleton/{pageType}', name: 'api_seo_singleton_get', methods: ['GET'])]
    #[IsGranted('ROLE_EDITOR')]
    public function getSingleton(string $pageType): JsonResponse
    {
        $entity = $this->findSingleton($pageType);
        return $this->get($pageType, 'singleton');
    }

    /**
     * Save SEO for a singleton page.
     */
    #[Route('/api/seo/singleton/{pageType}', name: 'api_seo_singleton_save', methods: ['PUT'])]
    #[IsGranted('ROLE_EDITOR')]
    public function saveSingleton(string $pageType, Request $request): JsonResponse
    {
        $entity = $this->findSingleton($pageType);
        $data = json_decode($request->getContent(), true);

        $seo = $entity->getSeoMetadata();
        if (!$seo) {
            $seo = new SeoMetadata();
            $this->em->persist($seo);
            $entity->setSeoMetadata($seo);
        }

        if (array_key_exists('ogType', $data)) $seo->setOgType($data['ogType']);
        if (array_key_exists('twitterCard', $data)) $seo->setTwitterCard($data['twitterCard']);
        if (array_key_exists('canonicalUrl', $data)) $seo->setCanonicalUrl($data['canonicalUrl']);
        if (array_key_exists('noIndex', $data)) $seo->setNoIndex($data['noIndex']);
        if (array_key_exists('noFollow', $data)) $seo->setNoFollow($data['noFollow']);

        if (isset($data['translations'])) {
            foreach ($data['translations'] as $locale => $fields) {
                $translation = $seo->translate($locale);
                if (!$translation) {
                    $translation = new SeoMetadataTranslation();
                    $translation->setLocale($locale);
                    $seo->addTranslation($translation);
                }
                if (array_key_exists('title', $fields)) $translation->setTitle($fields['title']);
                if (array_key_exists('description', $fields)) $translation->setDescription($fields['description']);
                if (array_key_exists('keywords', $fields)) $translation->setKeywords($fields['keywords']);
                if (array_key_exists('ogTitle', $fields)) $translation->setOgTitle($fields['ogTitle']);
                if (array_key_exists('ogDescription', $fields)) $translation->setOgDescription($fields['ogDescription']);
                if (array_key_exists('twitterTitle', $fields)) $translation->setTwitterTitle($fields['twitterTitle']);
                if (array_key_exists('twitterDescription', $fields)) $translation->setTwitterDescription($fields['twitterDescription']);
            }
        }

        $this->em->flush();
        return $this->json(['success' => true]);
    }

    /**
     * Generate SEO metadata using AI.
     * POST /api/seo/generate
     */
    #[Route('/api/seo/generate', name: 'api_seo_generate', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function generate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $entityType = $data['entityType'] ?? 'page';
        $locales = $data['locales'] ?? [['code' => 'hr', 'label' => 'Hrvatski'], ['code' => 'en', 'label' => 'English']];
        $content = $data['content'] ?? [];
        $siteName = $data['siteName'] ?? 'Go2Digital';

        try {
            $result = $this->seoGenerator->generate($entityType, $locales, $content, $siteName);
            return $this->json($result);
        } catch (\Throwable $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Public SEO data for a singleton page (no auth required).
     * Defined before the entity route so "singleton" isn't captured as {entityType}.
     */
    #[Route('/api/seo-public/singleton/{pageType}', name: 'api_seo_public_singleton_get', methods: ['GET'])]
    public function getSingletonPublic(string $pageType): JsonResponse
    {
        $entity = $this->findSingleton($pageType);
        $seo = $entity->getSeoMetadata();

        return $this->json($this->serializeSeoPublic($seo));
    }

    /**
     * Public SEO data for an entity (no auth required).
     */
    #[Route('/api/seo-public/{entityType}/{entityId}', name: 'api_seo_public_get', methods: ['GET'])]
    public function getPublic(string $entityType, string $entityId): JsonResponse
    {
        $entity = $this->findEntity($entityType, $entityId);
        $seo = $entity->getSeoMetadata();

        return $this->json($this->serializeSeoPublic($seo));
    }

    private function serializeSeoPublic(?SeoMetadata $seo): array
    {
        if (!$seo) return ['translations' => []];

        $translations = [];
        foreach (['hr', 'en'] as $locale) {
            $t = $seo->translate($locale);
            if ($t) {
                $translations[$locale] = $this->serializeTranslation($t);
            }
        }

        return [
            'ogType' => $seo->getOgType(),
            'twitterCard' => $seo->getTwitterCard(),
            'canonicalUrl' => $seo->getCanonicalUrl(),
            'robots' => $seo->getRobotsDirective(),
            'ogImageUrl' => $seo->getOgImage() ? '/storage/media/' . $seo->getOgImage()->getPath() : null,
            'translations' => $translations,
        ];
    }

    private function findSingleton(string $pageType): object
    {
        $class = self::SINGLETON_MAP[$pageType] ?? null;
        if (!$class) throw $this->createNotFoundException("Unknown page type: $pageType");
        $entity = $this->em->getRepository($class)->findOneBy([]);
        if (!$entity) throw $this->createNotFoundException("$pageType not found");
        return $entity;
    }

    private function findEntity(string $entityType, string $entityId): object
    {
        $class = self::ENTITY_MAP[$entityType] ?? null;
        if (!$class) {
            throw $this->createNotFoundException("Unknown entity type: $entityType");
        }

        $entity = $this->em->getRepository($class)->find(Uuid::fromRfc4122($entityId));
        if (!$entity) {
            throw $this->createNotFoundException("$entityType with id $entityId not found");
        }

        return $entity;
    }

    private function serializeTranslation(?object $t): array
    {
        if (!$t) return $this->emptyTranslation();

        return [
            'title' => $t->getTitle(),
            'description' => $t->getDescription(),
            'keywords' => $t->getKeywords(),
            'ogTitle' => $t->getOgTitle(),
            'ogDescription' => $t->getOgDescription(),
            'twitterTitle' => $t->getTwitterTitle(),
            'twitterDescription' => $t->getTwitterDescription(),
        ];
    }

    private function emptyTranslation(): array
    {
        return [
            'title' => null, 'description' => null, 'keywords' => null,
            'ogTitle' => null, 'ogDescription' => null,
            'twitterTitle' => null, 'twitterDescription' => null,
        ];
    }
}
