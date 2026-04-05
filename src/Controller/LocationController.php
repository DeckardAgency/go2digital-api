<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\SyncLog;
use App\Entity\Totem;
use App\Service\LocationSyncService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LocationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * Export locations in the exact same format as cdn.go2digital.hr/loc.json.
     * This replaces the CDN — Nuxt fetches from here.
     */
    #[Route('/api/locations', name: 'api_locations_export', methods: ['GET'])]
    public function export(): JsonResponse
    {
        $cities = $this->em->getRepository(City::class)->findBy(
            ['isActive' => true],
            ['sortOrder' => 'ASC']
        );

        $result = [];

        foreach ($cities as $city) {
            $totems = [];
            foreach ($city->getTotems() as $totem) {
                if (!$totem->isPublished()) {
                    continue;
                }

                $totems[] = [
                    'totem_id' => $totem->getCdnTotemId(),
                    'name' => $totem->getName(),
                    'name_en' => $totem->getNameEn() ?? '',
                    'totem_type' => $totem->getTotemType(),
                    'location' => $totem->getLocation(),
                    'screens' => $totem->getScreens(),
                    'images' => $totem->getImages() ?? [],
                    'floor_plans' => $totem->getFloorPlans() ?? [],
                    'header_image' => $totem->getHeaderImage(),
                    'is_installed' => $totem->isInstalled(),
                    'is_big_screen' => $totem->isBigScreen(),
                    'screen_width' => $totem->getScreenWidth(),
                    'screen_height' => $totem->getScreenHeight(),
                    'postbuy_category' => $totem->getPostbuyCategory(),
                    'ad_duration' => $totem->getAdDuration(),
                    'description' => $totem->getDescription(),
                    'description_en' => $totem->getDescriptionEn(),
                    'reach' => $totem->getReach(),
                    'video_url' => $totem->getVideoUrl(),
                    'totem_screens' => $totem->getTotemScreens() ?? [],
                    'totem_motion' => $totem->getTotemMotion(),
                ];
            }

            if (empty($totems)) {
                continue;
            }

            $result[] = [
                'city_id' => $city->getCdnCityId(),
                'name' => $city->getName(),
                'location' => $city->getLocation(),
                'totems' => $totems,
            ];
        }

        return $this->json($result);
    }

    /**
     * Trigger sync from CDN.
     */
    #[Route('/api/locations/sync', name: 'api_locations_sync', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function sync(LocationSyncService $syncService): JsonResponse
    {
        $user = $this->getUser();
        $start = microtime(true);

        $log = new SyncLog();
        $log->setTriggeredBy($user ? $user->getUserIdentifier() : 'system');

        try {
            $report = $syncService->sync();
            $log->setSuccess(true);
            $log->setReport($report);
        } catch (\Exception $e) {
            $log->setSuccess(false);
            $log->setError($e->getMessage());
            $log->setReport([]);
        }

        $log->setDurationMs(round((microtime(true) - $start) * 1000, 1));
        $this->em->persist($log);
        $this->em->flush();

        if ($log->isSuccess()) {
            return $this->json([
                'success' => true,
                'report' => $log->getReport(),
                'syncedAt' => $log->getCreatedAt()->format('c'),
                'durationMs' => $log->getDurationMs(),
            ]);
        }

        return $this->json([
            'success' => false,
            'error' => $log->getError(),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Sync a single totem from CDN by its cdnTotemId.
     */
    #[Route('/api/locations/totems/{id}/sync', name: 'api_locations_totem_sync', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function syncSingleTotem(string $id): JsonResponse
    {
        $totem = $this->em->getRepository(Totem::class)->find(Uuid::fromRfc4122($id));
        if (!$totem) {
            throw $this->createNotFoundException('Totem not found');
        }

        try {
            $response = $this->httpClient->request('GET', 'https://cdn.go2digital.hr/loc.json');
            $cdnData = $response->toArray();

            $cdnTotemId = $totem->getCdnTotemId();
            $found = false;

            foreach ($cdnData as $cityData) {
                foreach ($cityData['totems'] ?? [] as $totemData) {
                    if (($totemData['totem_id'] ?? 0) === $cdnTotemId) {
                        // Update all non-overridden fields
                        $fields = [
                            'name' => $totemData['name'] ?? '',
                            'nameEn' => $totemData['name_en'] ?? null,
                            'totemType' => $totemData['totem_type'] ?? null,
                            'location' => $totemData['location'] ?? null,
                            'screens' => $totemData['screens'] ?? 1,
                            'headerImage' => $totemData['header_image'] ?? null,
                            'isInstalled' => $totemData['is_installed'] ?? true,
                            'isBigScreen' => $totemData['is_big_screen'] ?? false,
                            'screenWidth' => $totemData['screen_width'] ?? 0,
                            'screenHeight' => $totemData['screen_height'] ?? 0,
                            'postbuyCategory' => $totemData['postbuy_category'] ?? null,
                            'adDuration' => $totemData['ad_duration'] ?? 10,
                            'description' => $totemData['description'] ?? null,
                            'descriptionEn' => $totemData['description_en'] ?? null,
                            'reach' => $totemData['reach'] ?? 0,
                            'videoUrl' => $totemData['video_url'] ?? null,
                            'totemMotion' => $totemData['totem_motion'] ?? null,
                        ];

                        $updated = [];
                        foreach ($fields as $field => $value) {
                            if (!$totem->isManuallyOverridden($field)) {
                                $setter = 'set'.ucfirst($field);
                                $totem->$setter($value);
                                $updated[] = $field;
                            }
                        }

                        $totem->setImages($totemData['images'] ?? null);
                        $totem->setFloorPlans($totemData['floor_plans'] ?? null);
                        $totem->setTotemScreens($totemData['totem_screens'] ?? null);
                        $totem->setLastSyncedAt(new \DateTimeImmutable());

                        $this->em->flush();
                        $found = true;

                        return $this->json([
                            'success' => true,
                            'updatedFields' => $updated,
                            'skippedFields' => array_keys(array_filter($fields, fn ($f) => $totem->isManuallyOverridden($f), ARRAY_FILTER_USE_KEY)),
                            'syncedAt' => (new \DateTimeImmutable())->format('c'),
                        ]);
                    }
                }
            }

            if (!$found) {
                return $this->json(['success' => false, 'error' => 'Totem not found in CDN data'], Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['success' => false]);
    }

    /**
     * List sync logs.
     */
    #[Route('/api/locations/sync-logs', name: 'api_locations_sync_logs', methods: ['GET'])]
    #[IsGranted('ROLE_EDITOR')]
    public function syncLogs(): JsonResponse
    {
        $logs = $this->em->getRepository(SyncLog::class)->findBy([], ['createdAt' => 'DESC'], 50);

        return $this->json(array_map(fn (SyncLog $log) => [
            'id' => $log->getId()->toRfc4122(),
            'success' => $log->isSuccess(),
            'report' => $log->getReport(),
            'error' => $log->getError(),
            'durationMs' => $log->getDurationMs(),
            'triggeredBy' => $log->getTriggeredBy(),
            'createdAt' => $log->getCreatedAt()->format('c'),
        ], $logs));
    }

    /**
     * Get sync status (last sync date, counts).
     */
    #[Route('/api/locations/sync-status', name: 'api_locations_sync_status', methods: ['GET'])]
    #[IsGranted('ROLE_EDITOR')]
    public function syncStatus(): JsonResponse
    {
        $cityCount = $this->em->getRepository(City::class)->count([]);
        $totemCount = $this->em->getRepository(Totem::class)->count([]);
        $publishedCount = $this->em->getRepository(Totem::class)->count(['isPublished' => true]);

        // Get last sync date from most recently synced city
        $lastCity = $this->em->getRepository(City::class)->findOneBy([], ['lastSyncedAt' => 'DESC']);
        $lastSyncedAt = $lastCity?->getLastSyncedAt()?->format('c');

        return $this->json([
            'totalCities' => $cityCount,
            'totalTotems' => $totemCount,
            'publishedTotems' => $publishedCount,
            'unpublishedTotems' => $totemCount - $publishedCount,
            'lastSyncedAt' => $lastSyncedAt,
        ]);
    }

    /**
     * List cities for CMS.
     */
    #[Route('/api/locations/cities', name: 'api_locations_cities', methods: ['GET'])]
    #[IsGranted('ROLE_EDITOR')]
    public function listCities(): JsonResponse
    {
        $cities = $this->em->getRepository(City::class)->findBy([], ['sortOrder' => 'ASC']);

        return $this->json(array_map(fn (City $c) => [
            'id' => $c->getId()->toRfc4122(),
            'cdnCityId' => $c->getCdnCityId(),
            'name' => $c->getName(),
            'location' => $c->getLocation(),
            'sortOrder' => $c->getSortOrder(),
            'isActive' => $c->isActive(),
            'totemCount' => $c->getTotems()->count(),
            'lastSyncedAt' => $c->getLastSyncedAt()?->format('c'),
        ], $cities));
    }

    /**
     * Update a city (sortOrder, isActive).
     */
    #[Route('/api/locations/cities/{id}', name: 'api_locations_city_update', methods: ['PATCH'])]
    #[IsGranted('ROLE_EDITOR')]
    public function updateCity(string $id, Request $request): JsonResponse
    {
        $city = $this->em->getRepository(City::class)->find(Uuid::fromRfc4122($id));
        if (!$city) {
            throw $this->createNotFoundException('City not found');
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['sortOrder'])) $city->setSortOrder($data['sortOrder']);
        if (isset($data['isActive'])) $city->setIsActive($data['isActive']);
        if (isset($data['name'])) $city->setName($data['name']);

        $this->em->flush();

        return $this->json(['success' => true]);
    }

    /**
     * List totems for CMS (with optional city filter).
     */
    #[Route('/api/locations/totems', name: 'api_locations_totems', methods: ['GET'])]
    #[IsGranted('ROLE_EDITOR')]
    public function listTotems(Request $request): JsonResponse
    {
        $criteria = [];
        if ($cityId = $request->query->get('cityId')) {
            $city = $this->em->getRepository(City::class)->find(Uuid::fromRfc4122($cityId));
            if ($city) {
                $criteria['city'] = $city;
            }
        }

        $totems = $this->em->getRepository(Totem::class)->findBy($criteria, ['sortOrder' => 'ASC']);

        return $this->json(array_map(fn (Totem $t) => [
            'id' => $t->getId()->toRfc4122(),
            'cdnTotemId' => $t->getCdnTotemId(),
            'name' => $t->getName(),
            'nameEn' => $t->getNameEn(),
            'cityName' => $t->getCity()?->getName(),
            'cityId' => $t->getCity()?->getId()->toRfc4122(),
            'totemType' => $t->getTotemType(),
            'postbuyCategory' => $t->getPostbuyCategory(),
            'screenWidth' => $t->getScreenWidth(),
            'screenHeight' => $t->getScreenHeight(),
            'reach' => $t->getReach(),
            'totemMotion' => $t->getTotemMotion(),
            'sortOrder' => $t->getSortOrder(),
            'isPublished' => $t->isPublished(),
            'isInstalled' => $t->isInstalled(),
            'manualOverrides' => $t->getManualOverrides(),
            'lastSyncedAt' => $t->getLastSyncedAt()?->format('c'),
        ], $totems));
    }

    /**
     * Get single totem for editing.
     */
    #[Route('/api/locations/totems/{id}', name: 'api_locations_totem_get', methods: ['GET'])]
    #[IsGranted('ROLE_EDITOR')]
    public function getTotem(string $id): JsonResponse
    {
        $t = $this->em->getRepository(Totem::class)->find(Uuid::fromRfc4122($id));
        if (!$t) {
            throw $this->createNotFoundException('Totem not found');
        }

        return $this->json([
            'id' => $t->getId()->toRfc4122(),
            'cdnTotemId' => $t->getCdnTotemId(),
            'name' => $t->getName(),
            'nameEn' => $t->getNameEn(),
            'cityId' => $t->getCity()?->getId()->toRfc4122(),
            'cityName' => $t->getCity()?->getName(),
            'totemType' => $t->getTotemType(),
            'location' => $t->getLocation(),
            'screens' => $t->getScreens(),
            'headerImage' => $t->getHeaderImage(),
            'isInstalled' => $t->isInstalled(),
            'isBigScreen' => $t->isBigScreen(),
            'screenWidth' => $t->getScreenWidth(),
            'screenHeight' => $t->getScreenHeight(),
            'postbuyCategory' => $t->getPostbuyCategory(),
            'adDuration' => $t->getAdDuration(),
            'description' => $t->getDescription(),
            'descriptionEn' => $t->getDescriptionEn(),
            'reach' => $t->getReach(),
            'videoUrl' => $t->getVideoUrl(),
            'totemMotion' => $t->getTotemMotion(),
            'sortOrder' => $t->getSortOrder(),
            'isPublished' => $t->isPublished(),
            'manualOverrides' => $t->getManualOverrides(),
            'images' => $t->getImages(),
            'floorPlans' => $t->getFloorPlans(),
            'totemScreens' => $t->getTotemScreens(),
            'lastSyncedAt' => $t->getLastSyncedAt()?->format('c'),
        ]);
    }

    /**
     * Update a totem. Marks edited fields as manually overridden.
     */
    #[Route('/api/locations/totems/{id}', name: 'api_locations_totem_update', methods: ['PATCH'])]
    #[IsGranted('ROLE_EDITOR')]
    public function updateTotem(string $id, Request $request): JsonResponse
    {
        $totem = $this->em->getRepository(Totem::class)->find(Uuid::fromRfc4122($id));
        if (!$totem) {
            throw $this->createNotFoundException('Totem not found');
        }

        $data = json_decode($request->getContent(), true);

        $editableFields = [
            'name', 'nameEn', 'totemType', 'location', 'screens',
            'headerImage', 'isInstalled', 'isBigScreen', 'screenWidth', 'screenHeight',
            'postbuyCategory', 'adDuration', 'description', 'descriptionEn',
            'reach', 'videoUrl', 'totemMotion', 'sortOrder', 'isPublished',
        ];

        foreach ($editableFields as $field) {
            if (array_key_exists($field, $data)) {
                $setter = 'set'.ucfirst($field);
                if (method_exists($totem, $setter)) {
                    $totem->$setter($data[$field]);
                    // Mark as manually overridden so sync won't overwrite
                    if (!in_array($field, ['sortOrder', 'isPublished'])) {
                        $totem->markManualOverride($field);
                    }
                }
            }
        }

        $this->em->flush();

        return $this->json(['success' => true]);
    }
}
