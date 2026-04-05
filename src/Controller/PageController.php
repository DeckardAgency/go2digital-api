<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Translation\PageTranslation;
use App\Enum\ContentStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

class PageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
    ) {
    }

    #[Route('/api/pages-manage', name: 'api_page_create', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $page = new Page();
        $this->applyData($page, $data);
        $this->applyTranslations($page, $data['translations'] ?? []);

        $this->em->persist($page);
        $this->em->flush();

        return $this->serializePage($page, Response::HTTP_CREATED);
    }

    #[Route('/api/pages-manage/{id}', name: 'api_page_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_EDITOR')]
    public function update(string $id, Request $request): JsonResponse
    {
        $page = $this->em->getRepository(Page::class)->find(Uuid::fromRfc4122($id));
        if (!$page) throw $this->createNotFoundException('Page not found');

        $data = json_decode($request->getContent(), true);
        $this->applyData($page, $data);
        $this->applyTranslations($page, $data['translations'] ?? []);

        $this->em->flush();

        return $this->serializePage($page);
    }

    private function applyData(Page $page, array $data): void
    {
        if (isset($data['slug'])) $page->setSlug($data['slug']);
        if (isset($data['status'])) $page->setStatus(ContentStatus::from($data['status']));
    }

    private function applyTranslations(Page $page, array $translations): void
    {
        foreach ($translations as $locale => $fields) {
            $translation = $page->translate($locale);
            if (!$translation) {
                $translation = new PageTranslation();
                $translation->setLocale($locale);
                $page->addTranslation($translation);
            }
            if (array_key_exists('title', $fields)) $translation->setTitle($fields['title']);
            if (array_key_exists('body', $fields)) $translation->setBody($fields['body']);
        }
    }

    private function serializePage(Page $page, int $status = Response::HTTP_OK): JsonResponse
    {
        $json = $this->serializer->serialize($page, 'json', [
            'circular_reference_handler' => fn ($object) => $object->getId()?->toRfc4122(),
        ]);
        return new JsonResponse($json, $status, [], true);
    }
}
