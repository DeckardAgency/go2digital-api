<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogPageContent;
use App\Entity\ContactPageContent;
use App\Entity\EsgPageContent;
use App\Entity\HomepageBillboard;
use App\Entity\HomepageCustomImage;
use App\Entity\HomepageCustomSolution;
use App\Entity\HomepageAnalytics;
use App\Entity\HomepageHero;
use App\Entity\HomepageHumanFocused;
use App\Entity\HomepageRentalsImage;
use App\Entity\HomepageTextAnimation;
use App\Entity\HomepageWhySection;
use App\Entity\LabPageContent;
use App\Entity\TeamPageContent;
use App\Entity\Media;
use App\Service\MediaLibraryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Handles GET/PUT for all singleton entities (single-row, no collection/create/delete).
 * Each entity type has exactly one row in the database.
 */
class SingletonController extends AbstractController
{
    private const SINGLETON_MAP = [
        // Homepage singletons
        'homepage-hero' => HomepageHero::class,
        'homepage-custom-solution' => HomepageCustomSolution::class,
        'homepage-human-focused' => HomepageHumanFocused::class,
        'homepage-text-animation' => HomepageTextAnimation::class,
        'homepage-billboard' => HomepageBillboard::class,
        'homepage-custom-image' => HomepageCustomImage::class,
        'homepage-rentals-image' => HomepageRentalsImage::class,
        'homepage-why-section' => HomepageWhySection::class,
        'homepage-analytics' => HomepageAnalytics::class,
        // Page content singletons
        'esg-page-content' => EsgPageContent::class,
        'blog-page-content' => BlogPageContent::class,
        'lab-page-content' => LabPageContent::class,
        'contact-page-content' => ContactPageContent::class,
        'team-page-content' => TeamPageContent::class,
    ];

    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
    ) {
    }

    #[Route('/api/singletons/{type}', name: 'api_singleton_get', methods: ['GET'])]
    public function get(string $type, Request $request): Response
    {
        $entityClass = $this->resolveEntityClass($type);
        $entity = $this->em->getRepository($entityClass)->findOneBy([]);

        if (null === $entity) {
            return $this->json(['error' => sprintf('No %s record found. Run fixtures to initialize.', $type)], Response::HTTP_NOT_FOUND);
        }

        $json = $this->serializer->serialize($entity, 'json', [
            'circular_reference_handler' => fn ($object) => $object->getId()?->toRfc4122(),
        ]);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/singletons/{type}', name: 'api_singleton_put', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_EDITOR')]
    public function put(string $type, Request $request): Response
    {
        $entityClass = $this->resolveEntityClass($type);
        $entity = $this->em->getRepository($entityClass)->findOneBy([]);

        if (null === $entity) {
            // Auto-create if doesn't exist
            $entity = new $entityClass();
            $this->em->persist($entity);
        }

        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $this->json(['error' => 'Invalid JSON body.'], Response::HTTP_BAD_REQUEST);
        }

        // Update non-translatable fields via setters
        foreach ($data as $key => $value) {
            if ('translations' === $key || 'id' === $key) {
                continue;
            }

            $setter = 'set'.ucfirst($key);
            if (method_exists($entity, $setter)) {
                $entity->$setter($value);
            }
        }

        // Update translations if provided
        if (isset($data['translations']) && is_array($data['translations'])) {
            foreach ($data['translations'] as $locale => $fields) {
                $translation = $entity->translate($locale);

                if (null === $translation) {
                    // Get translation class from the entity's OneToMany mapping
                    $translationClass = $this->getTranslationClass($entity);
                    if (null === $translationClass) {
                        continue;
                    }

                    $translation = new $translationClass();
                    $translation->setLocale($locale);
                    $entity->addTranslation($translation);
                }

                foreach ($fields as $field => $value) {
                    $setter = 'set'.ucfirst($field);
                    if (method_exists($translation, $setter)) {
                        $translation->$setter($value);
                    }
                }
            }
        }

        $this->em->flush();

        $json = $this->serializer->serialize($entity, 'json', [
            'circular_reference_handler' => fn ($object) => $object->getId()?->toRfc4122(),
        ]);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * Upload a media file to a specific field on a singleton entity.
     * E.g., POST /api/singletons/homepage-hero/media/video
     *       POST /api/singletons/homepage-hero/media/mobileVideo
     *       POST /api/singletons/homepage-custom-image/media/desktopImage
     */
    #[Route('/api/singletons/{type}/media/{field}', name: 'api_singleton_media_upload', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function uploadMedia(string $type, string $field, Request $request, MediaLibraryService $mediaLibrary): JsonResponse
    {
        $entityClass = $this->resolveEntityClass($type);
        $entity = $this->em->getRepository($entityClass)->findOneBy([]);

        if (null === $entity) {
            return $this->json(['error' => 'Singleton not found'], Response::HTTP_NOT_FOUND);
        }

        $setter = 'set'.ucfirst($field);
        if (!method_exists($entity, $setter)) {
            return $this->json(['error' => sprintf('Field "%s" does not exist on %s', $field, $type)], Response::HTTP_BAD_REQUEST);
        }

        $file = $request->files->get('file');
        if ($err = MediaLibraryService::validateUploadedFile($file)) {
            return $this->json(['error' => $err['message']], $err['status']);
        }

        $collection = str_contains($type, 'homepage') ? 'homepage' : 'general';
        $media = $mediaLibrary->upload($file, $collection);
        $entity->$setter($media);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'field' => $field,
            'mediaId' => $media->getId()->toRfc4122(),
            'url' => '/storage/media/'.$media->getPath(),
            'originalFilename' => $media->getOriginalFilename(),
            'mimeType' => $media->getMimeType(),
        ]);
    }

    /**
     * Remove a media reference from a singleton field.
     * E.g., DELETE /api/singletons/homepage-hero/media/video
     */
    #[Route('/api/singletons/{type}/media/{field}', name: 'api_singleton_media_remove', methods: ['DELETE'])]
    #[IsGranted('ROLE_EDITOR')]
    public function removeMedia(string $type, string $field): JsonResponse
    {
        $entityClass = $this->resolveEntityClass($type);
        $entity = $this->em->getRepository($entityClass)->findOneBy([]);

        if (null === $entity) {
            return $this->json(['error' => 'Singleton not found'], Response::HTTP_NOT_FOUND);
        }

        $setter = 'set'.ucfirst($field);
        if (!method_exists($entity, $setter)) {
            return $this->json(['error' => sprintf('Field "%s" does not exist on %s', $field, $type)], Response::HTTP_BAD_REQUEST);
        }

        $entity->$setter(null);
        $this->em->flush();

        return $this->json(['success' => true]);
    }

    private function resolveEntityClass(string $type): string
    {
        if (!isset(self::SINGLETON_MAP[$type])) {
            throw $this->createNotFoundException(sprintf('Unknown singleton type: %s. Available: %s', $type, implode(', ', array_keys(self::SINGLETON_MAP))));
        }

        return self::SINGLETON_MAP[$type];
    }

    private function getTranslationClass(object $entity): ?string
    {
        $reflection = new \ReflectionClass($entity);

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(\Doctrine\ORM\Mapping\OneToMany::class);
            foreach ($attributes as $attr) {
                $args = $attr->getArguments();
                if (isset($args['mappedBy']) && 'translatable' === $args['mappedBy']) {
                    return $args['targetEntity'];
                }
            }
        }

        return null;
    }
}
