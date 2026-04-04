<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\File;
use App\Entity\User;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FileUploadController extends AbstractController
{
    #[Route('/api/files/upload', name: 'api_files_upload', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function upload(
        Request $request,
        FileUploadService $fileService,
        ValidatorInterface $validator,
    ): JsonResponse {
        $file = $request->files->get('file');

        if (null === $file) {
            return $this->json(['error' => 'No file provided.'], Response::HTTP_BAD_REQUEST);
        }

        $violations = $validator->validate($file, [
            new Assert\File([
                'maxSize' => '50M',
            ]),
        ]);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            return $this->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $category = $request->request->get('category', 'general');
        $description = $request->request->get('description');

        /** @var User|null $user */
        $user = $this->getUser();

        $fileEntity = $fileService->upload($file, $category, $description, $user);

        return $this->json([
            'id' => $fileEntity->getId()->toRfc4122(),
            'filename' => $fileEntity->getFilename(),
            'originalFilename' => $fileEntity->getOriginalFilename(),
            'mimeType' => $fileEntity->getMimeType(),
            'size' => $fileEntity->getSize(),
            'category' => $fileEntity->getCategory(),
            'description' => $fileEntity->getDescription(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/files/{id}/download', name: 'api_files_download', methods: ['GET'])]
    public function download(
        string $id,
        EntityManagerInterface $em,
        FileUploadService $fileService,
    ): Response {
        $file = $em->getRepository(File::class)->find(Uuid::fromRfc4122($id));

        if (null === $file) {
            throw $this->createNotFoundException('File not found.');
        }

        $fullPath = $fileService->getFullPath($file);

        if (!file_exists($fullPath)) {
            throw $this->createNotFoundException('File not found on disk.');
        }

        $response = new BinaryFileResponse($fullPath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file->getOriginalFilename(),
        );

        return $response;
    }
}
