<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\LabProject;
use App\Entity\Setting;
use App\Enum\ContentStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

class FeaturedLabsController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * Returns featured lab projects based on mode (auto or manual).
     * Auto: last 3 published LabProjects with featured=true.
     * Manual: specific LabProjects by stored IDs.
     */
    #[Route('/api/homepage/featured-labs', name: 'api_homepage_featured_labs', methods: ['GET'])]
    public function getFeaturedLabs(Request $request): JsonResponse
    {
        $mode = $this->getSettingValue('homepage.featuredLabs.mode', 'auto');
        $selectedIds = $this->getSettingValue('homepage.featuredLabs.selectedProjectIds', []);

        $repo = $this->em->getRepository(LabProject::class);

        if ($mode === 'manual' && !empty($selectedIds)) {
            // Manual mode: fetch specific projects by IDs, preserve order
            $projects = [];
            foreach ($selectedIds as $id) {
                try {
                    $project = $repo->find(Uuid::fromRfc4122($id));
                    if ($project && $project->getStatus() === ContentStatus::Published) {
                        $projects[] = $project;
                    }
                } catch (\Exception) {
                    continue;
                }
            }
        } else {
            // Auto mode: last 3 featured published projects
            $qb = $repo->createQueryBuilder('p')
                ->where('p.featured = true')
                ->andWhere('p.status = :status')
                ->setParameter('status', ContentStatus::Published)
                ->orderBy('p.createdAt', 'DESC')
                ->setMaxResults(3);

            $projects = $qb->getQuery()->getResult();

            // Fallback: if no featured projects, return last 3 published
            if (empty($projects)) {
                $qb = $repo->createQueryBuilder('p')
                    ->where('p.status = :status')
                    ->setParameter('status', ContentStatus::Published)
                    ->orderBy('p.createdAt', 'DESC')
                    ->setMaxResults(3);

                $projects = $qb->getQuery()->getResult();
            }
        }

        // Build response with resolved image URLs and translations
        $locale = $request->headers->get('Accept-Language', 'hr');
        $projectsData = [];

        foreach ($projects as $project) {
            $translation = $project->translate($locale) ?? $project->translate('hr');
            $image = $project->getImage();

            $projectsData[] = [
                'id' => $project->getId()->toRfc4122(),
                'slug' => $project->getSlug(),
                'featured' => $project->isFeatured(),
                'status' => $project->getStatus()->value,
                'title' => $translation?->getTitle() ?? '',
                'shortTitle' => $translation?->getShortTitle() ?? '',
                'subtitle' => $translation?->getSubtitle() ?? '',
                'body' => $translation?->getBody() ?? '',
                'sections' => $translation?->getSections() ?? [],
                'image' => $image ? [
                    'id' => $image->getId()->toRfc4122(),
                    'path' => $image->getPath(),
                    'filename' => $image->getFilename(),
                    'thumbnails' => $image->getThumbnails(),
                    'focalX' => $image->getFocalX(),
                    'focalY' => $image->getFocalY(),
                ] : null,
                'categories' => array_map(
                    fn ($cat) => [
                        'slug' => $cat->getSlug(),
                        'name' => ($cat->translate($locale) ?? $cat->translate('hr'))?->getName() ?? $cat->getSlug(),
                    ],
                    $project->getCategories()->toArray()
                ),
            ];
        }

        return $this->json([
            'mode' => $mode,
            'projects' => $projectsData,
        ]);
    }

    /**
     * Get the current featured labs configuration.
     */
    #[Route('/api/homepage/featured-labs/config', name: 'api_homepage_featured_labs_config', methods: ['GET'])]
    public function getConfig(): JsonResponse
    {
        $mode = $this->getSettingValue('homepage.featuredLabs.mode', 'auto');
        $selectedIds = $this->getSettingValue('homepage.featuredLabs.selectedProjectIds', []);

        return $this->json([
            'mode' => $mode,
            'selectedProjectIds' => $selectedIds,
        ]);
    }

    /**
     * Update the featured labs configuration.
     */
    #[Route('/api/homepage/featured-labs/config', name: 'api_homepage_featured_labs_config_update', methods: ['PUT'])]
    #[IsGranted('ROLE_EDITOR')]
    public function updateConfig(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['mode'])) {
            $this->setSettingValue('homepage.featuredLabs.mode', $data['mode'], 'homepage');
        }

        if (isset($data['selectedProjectIds'])) {
            $this->setSettingValue('homepage.featuredLabs.selectedProjectIds', $data['selectedProjectIds'], 'homepage');
        }

        return $this->json([
            'mode' => $this->getSettingValue('homepage.featuredLabs.mode', 'auto'),
            'selectedProjectIds' => $this->getSettingValue('homepage.featuredLabs.selectedProjectIds', []),
        ]);
    }

    private function getSettingValue(string $key, mixed $default = null): mixed
    {
        $setting = $this->em->getRepository(Setting::class)->findOneBy(['key' => $key]);
        if (!$setting) {
            return $default;
        }

        $value = $setting->getValue();

        // If it's a simple string value stored as {"value": "auto"}, unwrap it
        if (is_array($value) && isset($value['value'])) {
            return $value['value'];
        }

        return $value;
    }

    private function setSettingValue(string $key, mixed $value, string $group): void
    {
        $repo = $this->em->getRepository(Setting::class);
        $setting = $repo->findOneBy(['key' => $key]);

        if (!$setting) {
            $setting = new Setting();
            $setting->setKey($key);
            $setting->setGroup($group);
            $this->em->persist($setting);
        }

        // Store as {"value": $value} for consistency
        $setting->setValue(['value' => $value]);
        $this->em->flush();
    }
}
