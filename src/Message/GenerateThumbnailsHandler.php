<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\Media;
use App\Service\ThumbnailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class GenerateThumbnailsHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private ThumbnailService $thumbnailService,
    ) {
    }

    public function __invoke(GenerateThumbnailsMessage $message): void
    {
        $media = $this->em->getRepository(Media::class)->find(Uuid::fromRfc4122($message->getMediaId()));

        if (null === $media) {
            return;
        }

        if (!$media->isImage()) {
            return;
        }

        $thumbnails = $this->thumbnailService->generate(
            $media->getPath(),
            $media->getFilename(),
        );

        if (!empty($thumbnails)) {
            $media->setThumbnails($thumbnails);
            $this->em->flush();
        }
    }
}
