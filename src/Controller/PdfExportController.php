<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\PdfExportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PdfExportController extends AbstractController
{
    public function __construct(
        private PdfExportService $pdfExport,
        private EntityManagerInterface $em,
    ) {}

    #[Route('/api/locations/export-pdf', name: 'api_locations_export_pdf', methods: ['POST'])]
    public function exportLocationsPdf(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $locationIds = $data['locationIds'] ?? [];

        if (empty($locationIds)) {
            return new JsonResponse(['error' => 'No location IDs provided'], Response::HTTP_BAD_REQUEST);
        }

        // Fetch locations from the database — try Totem entity first
        $locations = $this->resolveLocations($locationIds);

        if (empty($locations)) {
            return new JsonResponse(['error' => 'No locations found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $pdfContent = $this->pdfExport->generateLocationsPdf($locations);

            $filename = 'go2digital-locations-' . date('Y-m-d') . '.pdf';

            return new Response($pdfContent, Response::HTTP_OK, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => 'PDF generation failed',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/locations/preview-pdf', name: 'api_locations_preview_pdf', methods: ['GET'])]
    public function previewPdf(): Response
    {
        // Preview with sample data for CMS builder
        $sampleLocations = [
            ['name' => 'Zagrebačka avenija Tower istok', 'city' => 'Zagreb', 'environments' => ['City Light'], 'screens' => 4, 'lat' => 45.8009, 'lng' => 15.9714, 'image' => '', 'resolution' => '880x240 px', 'duration' => '10 minutes', 'type' => 'Full Motion', 'reach' => '2.1M'],
            ['name' => 'Arena Centar', 'city' => 'Zagreb', 'environments' => ['Indoor'], 'screens' => 8, 'lat' => 45.7739, 'lng' => 15.9351, 'image' => '', 'resolution' => '1920x1080 px', 'duration' => '15 minutes', 'type' => 'Full Motion', 'reach' => '1.5M'],
            ['name' => 'City Center One Split', 'city' => 'Split', 'environments' => ['Indoor'], 'screens' => 6, 'lat' => 43.5235, 'lng' => 16.4681, 'image' => '', 'resolution' => '1080x1920 px', 'duration' => '10 minutes', 'type' => 'Full Motion', 'reach' => '800K'],
            ['name' => 'Supernova Rijeka', 'city' => 'Rijeka', 'environments' => ['Indoor'], 'screens' => 3, 'lat' => 45.3271, 'lng' => 14.4422, 'image' => '', 'resolution' => '1920x1080 px', 'duration' => '10 minutes', 'type' => 'Static', 'reach' => '500K'],
        ];

        try {
            $pdfContent = $this->pdfExport->generateLocationsPdf($sampleLocations);

            return new Response($pdfContent, Response::HTTP_OK, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="preview.pdf"',
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => 'Preview failed',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function resolveLocations(array $ids): array
    {
        // Try to fetch from the locations API data (CDN-synced data)
        // The location IDs from the frontend might be external IDs or UUIDs
        $conn = $this->em->getConnection();

        // Try totems table
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "SELECT t.*, c.name as city_name FROM totems t LEFT JOIN cities c ON t.city_id = c.id WHERE t.external_id IN ({$placeholders}) OR LOWER(HEX(t.id)) IN ({$placeholders})";
            $params = array_merge($ids, array_map('strtolower', array_map(fn($id) => str_replace('-', '', $id), $ids)));
            $rows = $conn->fetchAllAssociative($sql, $params);

            if (!empty($rows)) {
                return array_map(function ($row) {
                    $manualOverrides = json_decode($row['manual_overrides'] ?? '{}', true) ?: [];
                    $images = json_decode($row['images'] ?? '[]', true) ?: [];
                    $w = $row['screen_width'] ?? '';
                    $h = $row['screen_height'] ?? '';
                    $motion = $row['totem_motion'] ?? '';

                    return [
                        'name' => $manualOverrides['name'] ?? $row['name'] ?? '',
                        'city' => $row['city_name'] ?? '',
                        'environments' => array_filter([$row['postbuy_category'] ?? '']),
                        'screens' => (int) ($row['screen_count'] ?? 0),
                        'lat' => (float) ($row['latitude'] ?? 0),
                        'lng' => (float) ($row['longitude'] ?? 0),
                        'image' => !empty($images[0]['main']) ? $images[0]['main'] : '',
                        'resolution' => ($w && $h) ? "{$w}x{$h} px" : '',
                        'duration' => $row['ad_duration'] ? $row['ad_duration'] . ' minutes' : '',
                        'type' => $motion ? ucfirst(str_replace('_', ' ', $motion)) : '',
                        'reach' => '', // Can be populated from analytics data
                    ];
                }, $rows);
            }
        } catch (\Throwable $e) {
            // Table might not exist or schema differs — fall through
        }

        // Fallback: return IDs as location names (frontend will pass full data in future)
        return array_map(fn($id) => [
            'name' => $id,
            'city' => '',
            'environments' => [],
            'screens' => 0,
            'lat' => 0,
            'lng' => 0,
            'image' => '',
        ], $ids);
    }
}
