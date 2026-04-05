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
use Symfony\Component\Uid\Uuid;

#[Route('/api/users')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private MailerInterface $mailer,
    ) {
    }

    #[Route('', name: 'api_users_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $users = $this->em->getRepository(User::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->json(array_map(fn(User $u) => $this->serialize($u), $users));
    }

    #[Route('/{id}', name: 'api_users_get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $user = $this->findUser($id);

        return $this->json($this->serialize($user));
    }

    #[Route('', name: 'api_users_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check duplicate email
        $existing = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email'] ?? '']);
        if ($existing) {
            return $this->json(['error' => 'A user with this email already exists.'], 409);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setRoles($data['roles'] ?? ['ROLE_EDITOR']);
        $user->setIsActive($data['isActive'] ?? true);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

        $this->em->persist($user);
        $this->em->flush();

        return $this->json($this->serialize($user), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_users_update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $user = $this->findUser($id);
        $data = json_decode($request->getContent(), true);

        if (isset($data['email']) && $data['email'] !== $user->getEmail()) {
            $existing = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existing) {
                return $this->json(['error' => 'A user with this email already exists.'], 409);
            }
            $user->setEmail($data['email']);
        }

        if (isset($data['firstName'])) $user->setFirstName($data['firstName']);
        if (isset($data['lastName'])) $user->setLastName($data['lastName']);
        if (isset($data['roles'])) $user->setRoles($data['roles']);
        if (isset($data['isActive'])) $user->setIsActive($data['isActive']);

        if (!empty($data['password'])) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        }

        $this->em->flush();

        return $this->json($this->serialize($user));
    }

    #[Route('/{id}', name: 'api_users_delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $user = $this->findUser($id);

        // Prevent self-deletion
        if ($user->getId()->equals($this->getUser()->getId())) {
            return $this->json(['error' => 'You cannot delete your own account.'], 400);
        }

        $this->em->remove($user);
        $this->em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Send a password reset email to a user.
     */
    #[Route('/{id}/send-reset', name: 'api_users_send_reset', methods: ['POST'])]
    public function sendReset(string $id, Request $request): JsonResponse
    {
        $user = $this->findUser($id);
        $data = json_decode($request->getContent(), true);
        $frontendUrl = $data['frontendUrl'] ?? 'http://localhost:4200';

        // Invalidate any existing tokens
        $this->em->createQueryBuilder()
            ->update(PasswordResetToken::class, 't')
            ->set('t.used', ':true')
            ->where('t.user = :user')
            ->andWhere('t.used = :false')
            ->setParameter('true', true)
            ->setParameter('false', false)
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();

        $token = new PasswordResetToken();
        $token->setUser($user);
        $this->em->persist($token);
        $this->em->flush();

        $resetUrl = rtrim($frontendUrl, '/') . '/reset-password?token=' . $token->getToken();

        $email = (new Email())
            ->to($user->getEmail())
            ->subject('Reset Your Password — Go2Digital CMS')
            ->html(
                '<div style="font-family: -apple-system, BlinkMacSystemFont, sans-serif; max-width: 480px; margin: 0 auto; padding: 40px 20px;">' .
                '<h2 style="color: #18181b; margin-bottom: 16px;">Reset Your Password</h2>' .
                '<p style="color: #71717a; font-size: 14px; line-height: 1.6;">Hi ' . htmlspecialchars($user->getFirstName()) . ',</p>' .
                '<p style="color: #71717a; font-size: 14px; line-height: 1.6;">A password reset was requested for your account. Click the button below to set a new password. This link expires in 1 hour.</p>' .
                '<div style="text-align: center; margin: 32px 0;">' .
                '<a href="' . $resetUrl . '" style="background-color: #18181b; color: #fff; padding: 12px 32px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500;">Reset Password</a>' .
                '</div>' .
                '<p style="color: #a1a1aa; font-size: 12px;">If you did not request this, you can safely ignore this email.</p>' .
                '</div>'
            );

        $this->mailer->send($email);

        return $this->json(['success' => true, 'message' => 'Reset email sent to ' . $user->getEmail()]);
    }

    private function findUser(string $id): User
    {
        $user = $this->em->getRepository(User::class)->find(Uuid::fromRfc4122($id));
        if (!$user) {
            throw $this->createNotFoundException("User not found");
        }
        return $user;
    }

    private function serialize(User $u): array
    {
        return [
            'id' => $u->getId()->toRfc4122(),
            'email' => $u->getEmail(),
            'firstName' => $u->getFirstName(),
            'lastName' => $u->getLastName(),
            'roles' => $u->getRoles(),
            'isActive' => $u->isActive(),
            'createdAt' => $u->getCreatedAt()?->format('c'),
            'updatedAt' => $u->getUpdatedAt()?->format('c'),
        ];
    }
}
