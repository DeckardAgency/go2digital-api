<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\MediaLibraryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MediaUploadController extends AbstractController
{
    #[Route('/api/media/upload', name: 'api_media_upload', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function upload(
        Request $request,
        MediaLibraryService $mediaLibrary,
        ValidatorInterface $validator,
    ): JsonResponse {
        $file = $request->files->get('file');

        if (null === $file) {
            return $this->json(['error' => 'No file provided.'], Response::HTTP_BAD_REQUEST);
        }

        // Validate file
        $violations = $validator->validate($file, [
            new Assert\File([
                'maxSize' => '50M',
                'mimeTypes' => [
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
                    'video/mp4', 'video/webm', 'video/quicktime',
                ],
                'mimeTypesMessage' => 'Please upload a valid image or video file.',
            ]),
        ]);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            return $this->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $collection = $request->request->get('collection', 'general');

        /** @var User|null $user */
        $user = $this->getUser();

        $media = $mediaLibrary->upload($file, $collection, $user);

        return $this->json([
            'id' => $media->getId()->toRfc4122(),
            'filename' => $media->getFilename(),
            'originalFilename' => $media->getOriginalFilename(),
            'mimeType' => $media->getMimeType(),
            'size' => $media->getSize(),
            'width' => $media->getWidth(),
            'height' => $media->getHeight(),
            'collection' => $media->getCollection(),
            'path' => $media->getPath(),
            'thumbnails' => $media->getThumbnails(),
            'url' => '/storage/media/'.$media->getPath(),
        ], Response::HTTP_CREATED);
    }
}
