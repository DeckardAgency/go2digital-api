<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/maintenance')]
#[IsGranted('ROLE_ADMIN')]
class MaintenanceController extends AbstractController
{
    public function __construct(
        private KernelInterface $kernel,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    #[Route('/cache/clear', name: 'api_maintenance_cache_clear', methods: ['POST'])]
    public function cacheClear(): JsonResponse
    {
        $result = $this->runCommand(['php', 'bin/console', 'cache:clear', '--env=' . $this->kernel->getEnvironment()]);

        return $this->json([
            'success' => $result['success'],
            'output' => $result['output'],
        ]);
    }

    #[Route('/cache/warmup', name: 'api_maintenance_cache_warmup', methods: ['POST'])]
    public function cacheWarmup(): JsonResponse
    {
        $result = $this->runCommand(['php', 'bin/console', 'cache:warmup', '--env=' . $this->kernel->getEnvironment()]);

        return $this->json([
            'success' => $result['success'],
            'output' => $result['output'],
        ]);
    }

    #[Route('/database/backup', name: 'api_maintenance_db_backup', methods: ['POST'])]
    public function databaseBackup(): JsonResponse
    {
        $dbUrl = $_ENV['DATABASE_URL'] ?? '';
        $parsed = parse_url($dbUrl);

        if (!$parsed || ($parsed['scheme'] ?? '') !== 'mysql') {
            return $this->json(['success' => false, 'error' => 'Only MySQL backups are supported.'], 400);
        }

        $host = $parsed['host'] ?? '127.0.0.1';
        $port = (string) ($parsed['port'] ?? '3306');
        $user = $parsed['user'] ?? 'root';
        $pass = $parsed['pass'] ?? '';
        $dbName = ltrim($parsed['path'] ?? '', '/');
        // Strip query string from dbName
        if (str_contains($dbName, '?')) {
            $dbName = substr($dbName, 0, strpos($dbName, '?'));
        }

        $backupDir = $this->kernel->getProjectDir() . '/var/backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = sprintf('%s_%s.sql', $dbName, date('Y-m-d_His'));
        $filepath = $backupDir . '/' . $filename;

        $cmd = ['mysqldump', '-h', $host, '-P', $port, '-u', $user];
        if ($pass) {
            $cmd[] = '-p' . $pass;
        }
        $cmd[] = $dbName;

        $process = new Process($cmd);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            return $this->json([
                'success' => false,
                'error' => $process->getErrorOutput(),
            ], 500);
        }

        file_put_contents($filepath, $process->getOutput());

        // Get file size
        $sizeBytes = filesize($filepath);
        $sizeMb = round($sizeBytes / 1024 / 1024, 2);

        return $this->json([
            'success' => true,
            'filename' => $filename,
            'size' => $sizeMb . ' MB',
            'path' => $filepath,
        ]);
    }

    #[Route('/database/backups', name: 'api_maintenance_db_backups_list', methods: ['GET'])]
    public function listBackups(): JsonResponse
    {
        $backupDir = $this->kernel->getProjectDir() . '/var/backups';
        $backups = [];

        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*.sql');
            rsort($files); // newest first

            foreach ($files as $file) {
                $sizeBytes = filesize($file);
                $backups[] = [
                    'filename' => basename($file),
                    'size' => round($sizeBytes / 1024 / 1024, 2) . ' MB',
                    'date' => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
        }

        return $this->json($backups);
    }

    /**
     * Download a backup file. Requires ROLE_SUPER_ADMIN + password confirmation.
     */
    #[Route('/database/backups/{filename}/download', name: 'api_maintenance_db_backup_download', methods: ['POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function downloadBackup(string $filename, Request $request): BinaryFileResponse|JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $password = $data['password'] ?? '';

        if (!$password) {
            return $this->json(['error' => 'Password is required to download backups.'], 403);
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['error' => 'Invalid password.'], 403);
        }

        // Sanitize filename to prevent path traversal
        $safeFilename = basename($filename);
        $filepath = $this->kernel->getProjectDir() . '/var/backups/' . $safeFilename;

        if (!file_exists($filepath) || !str_ends_with($safeFilename, '.sql')) {
            return $this->json(['error' => 'Backup not found.'], 404);
        }

        $response = new BinaryFileResponse($filepath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $safeFilename);

        return $response;
    }

    #[Route('/info', name: 'api_maintenance_info', methods: ['GET'])]
    public function systemInfo(): JsonResponse
    {
        return $this->json([
            'php' => PHP_VERSION,
            'symfony' => \Symfony\Component\HttpKernel\Kernel::VERSION,
            'environment' => $this->kernel->getEnvironment(),
            'debug' => $this->kernel->isDebug(),
        ]);
    }

    private function runCommand(array $cmd): array
    {
        $process = new Process($cmd, $this->kernel->getProjectDir());
        $process->setTimeout(120);
        $process->run();

        return [
            'success' => $process->isSuccessful(),
            'output' => trim($process->getOutput() . "\n" . $process->getErrorOutput()),
        ];
    }
}
