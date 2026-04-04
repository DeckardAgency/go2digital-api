<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogCategory;
use App\Entity\BlogPost;
use App\Entity\Translation\BlogPostTranslation;
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

class BlogPostController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
    ) {
    }

    #[Route('/api/blog-posts', name: 'api_blog_post_create', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $post = new BlogPost();
        $this->applyData($post, $data);
        $this->applyTranslations($post, $data['translations'] ?? []);

        $this->em->persist($post);
        $this->em->flush();

        return $this->serializePost($post, Response::HTTP_CREATED);
    }

    #[Route('/api/blog-posts/{id}', name: 'api_blog_post_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_EDITOR')]
    public function update(string $id, Request $request): JsonResponse
    {
        $post = $this->em->getRepository(BlogPost::class)->find(Uuid::fromRfc4122($id));
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $this->applyData($post, $data);
        $this->applyTranslations($post, $data['translations'] ?? []);

        $this->em->flush();

        return $this->serializePost($post);
    }

    private function applyData(BlogPost $post, array $data): void
    {
        if (isset($data['slug'])) {
            $post->setSlug($data['slug']);
        }
        if (isset($data['author'])) {
            $post->setAuthor($data['author']);
        }
        if (isset($data['date'])) {
            $post->setDate(new \DateTime($data['date']));
        }
        if (isset($data['status'])) {
            $post->setStatus(ContentStatus::from($data['status']));
        }
        if (array_key_exists('featured', $data)) {
            $post->setFeatured((bool) $data['featured']);
        }
        if (array_key_exists('category', $data)) {
            if ($data['category']) {
                // Handle IRI string like "/api/blog_categories/uuid"
                $catId = $data['category'];
                if (str_contains($catId, '/')) {
                    $catId = basename($catId);
                }
                $category = $this->em->getRepository(BlogCategory::class)->find(Uuid::fromRfc4122($catId));
                $post->setCategory($category);
            } else {
                $post->setCategory(null);
            }
        }
    }

    private function applyTranslations(BlogPost $post, array $translations): void
    {
        foreach ($translations as $locale => $fields) {
            $translation = $post->translate($locale);

            if (!$translation) {
                $translation = new BlogPostTranslation();
                $translation->setLocale($locale);
                $post->addTranslation($translation);
            }

            if (isset($fields['title'])) {
                $translation->setTitle($fields['title']);
            }
            if (array_key_exists('excerpt', $fields)) {
                $translation->setExcerpt($fields['excerpt']);
            }
            if (array_key_exists('body', $fields)) {
                $translation->setBody($fields['body']);
            }
        }
    }

    #[Route('/api/blog-posts/{id}/image', name: 'api_blog_post_image', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function uploadImage(string $id, Request $request, MediaLibraryService $mediaLibrary): JsonResponse
    {
        $post = $this->em->getRepository(BlogPost::class)->find(Uuid::fromRfc4122($id));
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $file = $request->files->get('image');
        if (!$file) {
            return $this->json(['error' => 'No image file provided'], Response::HTTP_BAD_REQUEST);
        }

        $media = $mediaLibrary->upload($file, 'blog');
        $post->setImage($media);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'mediaId' => $media->getId()->toRfc4122(),
            'imageUrl' => '/storage/media/' . $media->getPath(),
        ]);
    }

    #[Route('/api/blog-posts/{id}/image', name: 'api_blog_post_image_remove', methods: ['DELETE'])]
    #[IsGranted('ROLE_EDITOR')]
    public function removeImage(string $id): JsonResponse
    {
        $post = $this->em->getRepository(BlogPost::class)->find(Uuid::fromRfc4122($id));
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $post->setImage(null);
        $this->em->flush();

        return $this->json(['success' => true]);
    }

    private function serializePost(BlogPost $post, int $status = Response::HTTP_OK): JsonResponse
    {
        $json = $this->serializer->serialize($post, 'json', [
            'circular_reference_handler' => fn ($object) => $object->getId()?->toRfc4122(),
        ]);

        return new JsonResponse($json, $status, [], true);
    }
}
