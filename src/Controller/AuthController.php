<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    /**
     * Public: reset password using a token (sent via email).
     */
    #[Route('/api/auth/reset-password', name: 'api_auth_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tokenStr = $data['token'] ?? '';
        $newPassword = $data['password'] ?? '';

        if (!$tokenStr || !$newPassword) {
            return $this->json(['error' => 'Token and password are required.'], 400);
        }

        $token = $this->em->getRepository(PasswordResetToken::class)->findOneBy(['token' => $tokenStr]);

        if (!$token || !$token->isValid()) {
            return $this->json(['error' => 'Invalid or expired reset token.'], 400);
        }

        $user = $token->getUser();
        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $token->setUsed(true);
        $this->em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/api/auth/me', name: 'api_auth_me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId()->toRfc4122(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'roles' => $user->getRoles(),
            'isActive' => $user->isActive(),
        ]);
    }
}
