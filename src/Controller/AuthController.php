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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private MailerInterface $mailer,
    ) {
    }

    /**
     * Public: request a password reset email.
     */
    #[Route('/api/auth/request-reset', name: 'api_auth_request_reset', methods: ['POST'])]
    public function requestReset(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';

        // Always return success to prevent email enumeration
        if (!$email) {
            return $this->json(['success' => true]);
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user && $user->isActive()) {
            $token = new PasswordResetToken();
            $token->setUser($user);
            $this->em->persist($token);
            $this->em->flush();

            $cmsUrl = $_ENV['CMS_URL'] ?? 'https://cms.go2digital.hr';
            $resetLink = $cmsUrl . '/reset-password?token=' . $token->getToken();

            try {
                $emailMessage = (new Email())
                    ->from($_ENV['MAILER_FROM'] ?? 'noreply@go2digital.hr')
                    ->to($user->getEmail())
                    ->subject('Reset your password — Go2Digital CMS')
                    ->html(
                        '<div style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,sans-serif;max-width:480px;margin:0 auto;padding:40px 20px;">' .
                        '<div style="text-align:center;margin-bottom:32px;">' .
                        '<div style="display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;background:#18181b;border-radius:12px;margin-bottom:16px;">' .
                        '<span style="color:#fff;font-weight:700;font-size:14px;">G2D</span></div>' .
                        '<h1 style="font-size:20px;font-weight:600;color:#18181b;margin:0;">Reset Your Password</h1></div>' .
                        '<p style="font-size:14px;color:#52525b;line-height:1.6;margin:0 0 24px;">You requested a password reset for your Go2Digital CMS account. Click the button below to set a new password. This link expires in 1 hour.</p>' .
                        '<div style="text-align:center;margin:32px 0;">' .
                        '<a href="' . $resetLink . '" style="display:inline-block;padding:12px 32px;background:#18181b;color:#fff;text-decoration:none;border-radius:8px;font-size:14px;font-weight:500;">Reset Password</a></div>' .
                        '<p style="font-size:12px;color:#a1a1aa;line-height:1.5;margin:24px 0 0;">If you didn\'t request this, you can safely ignore this email. Your password won\'t change.</p>' .
                        '<hr style="border:none;border-top:1px solid #e4e4e7;margin:32px 0 16px;">' .
                        '<p style="font-size:11px;color:#d4d4d8;text-align:center;margin:0;">Go2Digital CMS</p></div>'
                    );
                $this->mailer->send($emailMessage);
            } catch (\Throwable) {
                // Log error but don't expose to user
            }
        }

        return $this->json(['success' => true]);
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
            'createdAt' => $user->getCreatedAt()?->format('c'),
        ]);
    }

    /**
     * Update current user's profile (name, email).
     */
    #[Route('/api/auth/profile', name: 'api_auth_profile_update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (isset($data['firstName'])) $user->setFirstName($data['firstName']);
        if (isset($data['lastName'])) $user->setLastName($data['lastName']);

        if (isset($data['email']) && $data['email'] !== $user->getEmail()) {
            $existing = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existing) {
                return $this->json(['error' => 'This email is already in use.'], 409);
            }
            $user->setEmail($data['email']);
        }

        $this->em->flush();

        return $this->json([
            'success' => true,
            'id' => $user->getId()->toRfc4122(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * Change current user's password.
     */
    #[Route('/api/auth/change-password', name: 'api_auth_change_password', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function changePassword(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $currentPassword = $data['currentPassword'] ?? '';
        $newPassword = $data['newPassword'] ?? '';

        if (!$currentPassword || !$newPassword) {
            return $this->json(['error' => 'Current and new password are required.'], 400);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            return $this->json(['error' => 'Current password is incorrect.'], 403);
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $this->em->flush();

        return $this->json(['success' => true]);
    }
}
