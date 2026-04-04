<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\BlogPost;
use App\Entity\LabProject;
use App\Entity\Page;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Controls access to content entities (BlogPost, LabProject, Page).
 * ROLE_EDITOR can create and edit.
 * ROLE_ADMIN can delete.
 */
class ContentVoter extends Voter
{
    public const CREATE = 'CONTENT_CREATE';
    public const EDIT = 'CONTENT_EDIT';
    public const DELETE = 'CONTENT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CREATE, self::EDIT, self::DELETE])) {
            return false;
        }

        return $subject instanceof BlogPost
            || $subject instanceof LabProject
            || $subject instanceof Page;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        $roles = $user->getRoles();

        return match ($attribute) {
            self::CREATE, self::EDIT => in_array('ROLE_EDITOR', $roles) || in_array('ROLE_ADMIN', $roles),
            self::DELETE => in_array('ROLE_ADMIN', $roles),
            default => false,
        };
    }
}
