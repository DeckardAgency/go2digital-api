<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogCategory;
use App\Entity\LabCategory;
use App\Entity\Translation\BlogCategoryTranslation;
use App\Entity\Translation\LabCategoryTranslation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Custom create/update for category entities that have translations.
 * API Platform's default POST/PATCH can't handle nested translations.
 */
class CategoryController extends AbstractController
{
    private const MAP = [
        'blog' => [
            'entity' => BlogCategory::class,
            'translation' => BlogCategoryTranslation::class,
            'fields' => ['name'],
        ],
        'lab' => [
            'entity' => LabCategory::class,
            'translation' => LabCategoryTranslation::class,
            'fields' => ['name'],
        ],
    ];

    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
    ) {
    }

    #[Route('/api/blog-categories', name: 'api_blog_category_create', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function createBlogCategory(Request $request): JsonResponse
    {
        return $this->handleCreate('blog', $request);
    }

    #[Route('/api/blog-categories/{id}', name: 'api_blog_category_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_EDITOR')]
    public function updateBlogCategory(string $id, Request $request): JsonResponse
    {
        return $this->handleUpdate('blog', $id, $request);
    }

    #[Route('/api/lab-categories', name: 'api_lab_category_create', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function createLabCategory(Request $request): JsonResponse
    {
        return $this->handleCreate('lab', $request);
    }

    #[Route('/api/lab-categories/{id}', name: 'api_lab_category_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_EDITOR')]
    public function updateLabCategory(string $id, Request $request): JsonResponse
    {
        return $this->handleUpdate('lab', $id, $request);
    }

    private function handleCreate(string $type, Request $request): JsonResponse
    {
        $config = self::MAP[$type];
        $data = json_decode($request->getContent(), true);

        $entity = new $config['entity']();
        $this->applyData($entity, $data);
        $this->applyTranslations($entity, $data['translations'] ?? [], $config);

        $this->em->persist($entity);
        $this->em->flush();

        return $this->serializeEntity($entity, Response::HTTP_CREATED);
    }

    private function handleUpdate(string $type, string $id, Request $request): JsonResponse
    {
        $config = self::MAP[$type];
        $entity = $this->em->getRepository($config['entity'])->find(Uuid::fromRfc4122($id));
        if (!$entity) {
            throw $this->createNotFoundException("Category not found");
        }

        $data = json_decode($request->getContent(), true);
        $this->applyData($entity, $data);
        $this->applyTranslations($entity, $data['translations'] ?? [], $config);

        $this->em->flush();

        return $this->serializeEntity($entity);
    }

    private function applyData(object $entity, array $data): void
    {
        if (isset($data['slug'])) {
            $entity->setSlug($data['slug']);
        }
        if (array_key_exists('sortOrder', $data)) {
            $entity->setSortOrder((int) $data['sortOrder']);
        }
    }

    private function applyTranslations(object $entity, array $translations, array $config): void
    {
        foreach ($translations as $locale => $fields) {
            $translation = $entity->translate($locale);

            if (!$translation) {
                $translationClass = $config['translation'];
                $translation = new $translationClass();
                $translation->setLocale($locale);
                $entity->addTranslation($translation);
            }

            foreach ($config['fields'] as $field) {
                if (array_key_exists($field, $fields)) {
                    $setter = 'set' . ucfirst($field);
                    $translation->$setter($fields[$field]);
                }
            }
        }
    }

    private function serializeEntity(object $entity, int $status = Response::HTTP_OK): JsonResponse
    {
        $json = $this->serializer->serialize($entity, 'json', [
            'circular_reference_handler' => fn ($object) => $object->getId()?->toRfc4122(),
        ]);

        return new JsonResponse($json, $status, [], true);
    }
}
