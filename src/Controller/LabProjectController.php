<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\LabCategory;
use App\Entity\LabProject;
use App\Entity\Translation\LabProjectTranslation;
use App\Enum\ContentStatus;
use App\Service\MediaLibraryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

class LabProjectController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
    ) {
    }

    #[Route('/api/lab-projects', name: 'api_lab_project_create', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $project = new LabProject();
        $this->applyData($project, $data);
        $this->applyTranslations($project, $data['translations'] ?? []);

        $this->em->persist($project);
        $this->em->flush();

        return $this->serializeProject($project, Response::HTTP_CREATED);
    }

    #[Route('/api/lab-projects/{id}', name: 'api_lab_project_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_EDITOR')]
    public function update(string $id, Request $request): JsonResponse
    {
        $project = $this->em->getRepository(LabProject::class)->find(Uuid::fromRfc4122($id));
        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $this->applyData($project, $data);
        $this->applyTranslations($project, $data['translations'] ?? []);

        $this->em->flush();

        return $this->serializeProject($project);
    }

    private function applyData(LabProject $project, array $data): void
    {
        if (isset($data['slug'])) {
            $project->setSlug($data['slug']);
        }
        if (isset($data['status'])) {
            $project->setStatus(ContentStatus::from($data['status']));
        }
        if (array_key_exists('featured', $data)) {
            $project->setFeatured((bool) $data['featured']);
        }
        if (isset($data['categories'])) {
            // Clear existing categories
            foreach ($project->getCategories() as $cat) {
                $project->removeCategory($cat);
            }
            // Add new categories from IRI strings or UUIDs
            foreach ($data['categories'] as $catRef) {
                if (empty($catRef)) {
                    continue;
                }
                $catId = $catRef;
                if (str_contains($catId, '/')) {
                    $catId = basename($catId);
                }
                try {
                    $category = $this->em->getRepository(LabCategory::class)->find(Uuid::fromRfc4122($catId));
                    if ($category) {
                        $project->addCategory($category);
                    }
                } catch (\Exception) {
                    continue;
                }
            }
        }
    }

    private function applyTranslations(LabProject $project, array $translations): void
    {
        foreach ($translations as $locale => $fields) {
            $translation = $project->translate($locale);

            if (!$translation) {
                $translation = new LabProjectTranslation();
                $translation->setLocale($locale);
                $project->addTranslation($translation);
            }

            if (isset($fields['title'])) {
                $translation->setTitle($fields['title']);
            }
            if (array_key_exists('shortTitle', $fields)) {
                $translation->setShortTitle($fields['shortTitle']);
            }
            if (array_key_exists('subtitle', $fields)) {
                $translation->setSubtitle($fields['subtitle']);
            }
            if (array_key_exists('body', $fields)) {
                $translation->setBody($fields['body']);
            }
            if (array_key_exists('sections', $fields)) {
                $translation->setSections($fields['sections']);
            }
        }
    }

    #[Route('/api/lab-projects/{id}/image', name: 'api_lab_project_image', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function uploadImage(string $id, Request $request, MediaLibraryService $mediaLibrary): JsonResponse
    {
        $project = $this->em->getRepository(LabProject::class)->find(Uuid::fromRfc4122($id));
        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $file = $request->files->get('image');
        if (!$file) {
            return $this->json(['error' => 'No image file provided'], Response::HTTP_BAD_REQUEST);
        }

        $media = $mediaLibrary->upload($file, 'labs');
        $project->setImage($media);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'mediaId' => $media->getId()->toRfc4122(),
            'imageUrl' => '/storage/media/' . $media->getPath(),
        ]);
    }

    #[Route('/api/lab-projects/{id}/focal-point', name: 'api_lab_project_focal_point', methods: ['PUT'])]
    #[IsGranted('ROLE_EDITOR')]
    public function updateFocalPoint(string $id, Request $request): JsonResponse
    {
        $project = $this->em->getRepository(LabProject::class)->find(Uuid::fromRfc4122($id));
        if (!$project || !$project->getImage()) {
            throw $this->createNotFoundException('Project or image not found');
        }

        $data = json_decode($request->getContent(), true);
        $media = $project->getImage();
        $media->setFocalX((float) ($data['x'] ?? 50));
        $media->setFocalY((float) ($data['y'] ?? 50));
        $this->em->flush();

        return $this->json(['success' => true, 'focalX' => $media->getFocalX(), 'focalY' => $media->getFocalY()]);
    }

    #[Route('/api/lab-projects/{id}/image', name: 'api_lab_project_image_remove', methods: ['DELETE'])]
    #[IsGranted('ROLE_EDITOR')]
    public function removeImage(string $id): JsonResponse
    {
        $project = $this->em->getRepository(LabProject::class)->find(Uuid::fromRfc4122($id));
        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $project->setImage(null);
        $this->em->flush();

        return $this->json(['success' => true]);
    }

    private function serializeProject(LabProject $project, int $status = Response::HTTP_OK): JsonResponse
    {
        $json = $this->serializer->serialize($project, 'json', [
            'circular_reference_handler' => fn ($object) => $object->getId()?->toRfc4122(),
        ]);

        return new JsonResponse($json, $status, [], true);
    }
}
