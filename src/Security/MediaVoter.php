<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Media;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Controls access to Media entities.
 * ROLE_EDITOR can upload and edit metadata.
 * ROLE_ADMIN or the original uploader can delete.
 */
class MediaVoter extends Voter
{
    public const UPLOAD = 'MEDIA_UPLOAD';
    public const EDIT = 'MEDIA_EDIT';
    public const DELETE = 'MEDIA_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::UPLOAD, self::EDIT, self::DELETE])) {
            return false;
        }

        // UPLOAD doesn't require a subject (media doesn't exist yet)
        if (self::UPLOAD === $attribute) {
            return true;
        }

        return $subject instanceof Media;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        $roles = $user->getRoles();

        return match ($attribute) {
            self::UPLOAD, self::EDIT => in_array('ROLE_EDITOR', $roles) || in_array('ROLE_ADMIN', $roles),
            self::DELETE => $this->canDelete($subject, $user, $roles),
            default => false,
        };
    }

    private function canDelete(Media $media, UserInterface $user, array $roles): bool
    {
        // Admin can delete anything
        if (in_array('ROLE_ADMIN', $roles)) {
            return true;
        }

        // Original uploader can delete their own media
        $uploader = $media->getUploadedBy();

        if ($uploader instanceof User && $user instanceof User) {
            return $uploader->getId()->equals($user->getId());
        }

        return false;
    }
}
