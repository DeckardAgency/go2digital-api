<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class RootController extends AbstractController
{
    #[Route('/', name: 'api_root', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
        ]);
    }
}
