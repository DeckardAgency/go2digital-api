<?php

declare(strict_types=1);

namespace App\Message;

class GenerateThumbnailsMessage
{
    public function __construct(
        private readonly string $mediaId,
    ) {
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }
}
