<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\City;
use App\Entity\Totem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LocationSyncService
{
    private const CDN_URL = 'https://cdn.go2digital.hr/loc.json';

    public function __construct(
        private EntityManagerInterface $em,
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * Sync locations from CDN.
     * Returns a report of what changed.
     */
    public function sync(): array
    {
        $response = $this->httpClient->request('GET', self::CDN_URL);
        $cdnData = $response->toArray();

        $now = new \DateTimeImmutable();
        $report = ['cities_created' => 0, 'cities_updated' => 0, 'totems_created' => 0, 'totems_updated' => 0, 'totems_unchanged' => 0, 'total_cities' => 0, 'total_totems' => 0];

        $cityRepo = $this->em->getRepository(City::class);
        $totemRepo = $this->em->getRepository(Totem::class);

        foreach ($cdnData as $cityIndex => $cityData) {
            $report['total_cities']++;

            // Find or create city by CDN ID
            $city = $cityRepo->findOneBy(['cdnCityId' => $cityData['city_id']]);

            if (!$city) {
                $city = new City();
                $city->setCdnCityId($cityData['city_id']);
                $city->setSortOrder($cityIndex + 1);
                $this->em->persist($city);
                $report['cities_created']++;
            } else {
                $report['cities_updated']++;
            }

            $city->setName($cityData['name']);
            $city->setLocation($cityData['location'] ?? null);
            $city->setLastSyncedAt($now);

            // Process totems
            foreach ($cityData['totems'] ?? [] as $totemIndex => $totemData) {
                $report['total_totems']++;

                $totem = $totemRepo->findOneBy(['cdnTotemId' => $totemData['totem_id']]);

                if (!$totem) {
                    $totem = new Totem();
                    $totem->setCdnTotemId($totemData['totem_id']);
                    $totem->setSortOrder($totemIndex + 1);
                    $city->addTotem($totem);
                    $this->em->persist($totem);
                    $report['totems_created']++;
                } else {
                    // Only update fields that haven't been manually overridden
                    $report['totems_updated']++;
                }

                // Update all non-overridden fields
                $this->syncTotemField($totem, 'name', $totemData['name'] ?? '');
                $this->syncTotemField($totem, 'nameEn', $totemData['name_en'] ?? null);
                $this->syncTotemField($totem, 'totemType', $totemData['totem_type'] ?? null);
                $this->syncTotemField($totem, 'location', $totemData['location'] ?? null);
                $this->syncTotemField($totem, 'screens', $totemData['screens'] ?? 1);
                $this->syncTotemField($totem, 'headerImage', $totemData['header_image'] ?? null);
                $this->syncTotemField($totem, 'isInstalled', $totemData['is_installed'] ?? true);
                $this->syncTotemField($totem, 'isBigScreen', $totemData['is_big_screen'] ?? false);
                $this->syncTotemField($totem, 'screenWidth', $totemData['screen_width'] ?? 0);
                $this->syncTotemField($totem, 'screenHeight', $totemData['screen_height'] ?? 0);
                $this->syncTotemField($totem, 'postbuyCategory', $totemData['postbuy_category'] ?? null);
                $this->syncTotemField($totem, 'adDuration', $totemData['ad_duration'] ?? 10);
                $this->syncTotemField($totem, 'description', $totemData['description'] ?? null);
                $this->syncTotemField($totem, 'descriptionEn', $totemData['description_en'] ?? null);
                $this->syncTotemField($totem, 'reach', $totemData['reach'] ?? 0);
                $this->syncTotemField($totem, 'videoUrl', $totemData['video_url'] ?? null);
                $this->syncTotemField($totem, 'totemMotion', $totemData['totem_motion'] ?? null);

                // Always sync these (structured data, not typically manually edited)
                $totem->setImages($totemData['images'] ?? null);
                $totem->setFloorPlans($totemData['floor_plans'] ?? null);
                $totem->setTotemScreens($totemData['totem_screens'] ?? null);
                $totem->setLastSyncedAt($now);
            }
        }

        $this->em->flush();

        return $report;
    }

    private function syncTotemField(Totem $totem, string $field, mixed $value): void
    {
        // Skip if manually overridden
        if ($totem->isManuallyOverridden($field)) {
            return;
        }

        $setter = 'set'.ucfirst($field);
        if (method_exists($totem, $setter)) {
            $totem->$setter($value);
        }
    }
}
