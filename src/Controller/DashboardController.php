<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\LabProject;
use App\Entity\Media;
use App\Entity\Page;
use App\Entity\Setting;
use App\Entity\TeamMember;
use App\Entity\Totem;
use App\Entity\City;
use App\Enum\ContentStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/dashboard')]
#[IsGranted('ROLE_EDITOR')]
class DashboardController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('/stats', name: 'api_dashboard_stats', methods: ['GET'])]
    public function stats(): JsonResponse
    {
        $blogRepo = $this->em->getRepository(BlogPost::class);
        $labRepo = $this->em->getRepository(LabProject::class);
        $mediaRepo = $this->em->getRepository(Media::class);

        // Counts
        $blogTotal = $blogRepo->count([]);
        $blogPublished = $blogRepo->count(['status' => ContentStatus::Published]);
        $blogDraft = $blogRepo->count(['status' => ContentStatus::Draft]);

        $labTotal = $labRepo->count([]);
        $labPublished = $labRepo->count(['status' => ContentStatus::Published]);
        $labDraft = $labRepo->count(['status' => ContentStatus::Draft]);

        $mediaTotal = $mediaRepo->count([]);
        $pageTotal = $this->em->getRepository(Page::class)->count([]);
        $teamTotal = $this->em->getRepository(TeamMember::class)->count([]);
        $totemTotal = $this->em->getRepository(Totem::class)->count([]);
        $cityTotal = $this->em->getRepository(City::class)->count([]);
        $settingTotal = $this->em->getRepository(Setting::class)->count([]);

        // Recent blog posts (last 5)
        $recentPosts = $blogRepo->createQueryBuilder('p')
            ->select('p.id', 'p.slug', 'p.status', 'p.date', 'p.author', 'p.createdAt')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Recent lab projects (last 5)
        $recentProjects = $labRepo->createQueryBuilder('p')
            ->select('p.id', 'p.slug', 'p.status', 'p.createdAt')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Recent media (last 8)
        $recentMedia = $mediaRepo->createQueryBuilder('m')
            ->select('m.id', 'm.originalFilename', 'm.path', 'm.mimeType', 'm.size', 'm.collection', 'm.createdAt')
            ->orderBy('m.createdAt', 'DESC')
            ->setMaxResults(8)
            ->getQuery()
            ->getResult();

        // Media size total
        $mediaSizeTotal = $mediaRepo->createQueryBuilder('m')
            ->select('SUM(m.size)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        // Format recent items
        $formatPosts = array_map(fn($p) => [
            'id' => $p['id']->toRfc4122(),
            'slug' => $p['slug'],
            'status' => $p['status']->value,
            'date' => $p['date']?->format('Y-m-d'),
            'author' => $p['author'],
            'createdAt' => $p['createdAt']->format('c'),
        ], $recentPosts);

        $formatProjects = array_map(fn($p) => [
            'id' => $p['id']->toRfc4122(),
            'slug' => $p['slug'],
            'status' => $p['status']->value,
            'createdAt' => $p['createdAt']->format('c'),
        ], $recentProjects);

        $formatMedia = array_map(fn($m) => [
            'id' => $m['id']->toRfc4122(),
            'filename' => $m['originalFilename'],
            'path' => $m['path'],
            'mimeType' => $m['mimeType'],
            'size' => $m['size'],
            'collection' => $m['collection'],
            'createdAt' => $m['createdAt']->format('c'),
        ], $recentMedia);

        return $this->json([
            'counts' => [
                'blogPosts' => ['total' => $blogTotal, 'published' => $blogPublished, 'draft' => $blogDraft],
                'labProjects' => ['total' => $labTotal, 'published' => $labPublished, 'draft' => $labDraft],
                'media' => ['total' => $mediaTotal, 'totalSizeBytes' => (int) $mediaSizeTotal],
                'pages' => $pageTotal,
                'team' => $teamTotal,
                'totems' => $totemTotal,
                'cities' => $cityTotal,
                'settings' => $settingTotal,
            ],
            'recent' => [
                'posts' => $formatPosts,
                'projects' => $formatProjects,
                'media' => $formatMedia,
            ],
        ]);
    }
}
