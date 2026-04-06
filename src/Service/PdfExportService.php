<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Twig\Environment;

class PdfExportService
{
    private const DEFAULT_LAYOUT = [
        'pageSize' => 'A4',
        'orientation' => 'portrait',
        'margins' => ['top' => 15, 'right' => 15, 'bottom' => 15, 'left' => 15],
        'header' => [
            'title' => 'Go2Digital',
            'subtitle' => 'Location Collection',
            'logo' => '',
            'bgColor' => '#03120F',
            'textColor' => '#ffffff',
            'accentColor' => '#0CD459',
        ],
        'columns' => [
            ['key' => 'index', 'label' => '#', 'enabled' => true],
            ['key' => 'name', 'label' => 'Location Name', 'enabled' => true],
            ['key' => 'city', 'label' => 'City', 'enabled' => true],
            ['key' => 'environment', 'label' => 'Environment', 'enabled' => true],
            ['key' => 'screens', 'label' => 'Screens', 'enabled' => true],
            ['key' => 'coordinates', 'label' => 'Coordinates', 'enabled' => true],
            ['key' => 'image', 'label' => 'Image', 'enabled' => false],
        ],
        'footer' => [
            'text' => 'go2digital.hr',
            'showPageNumbers' => true,
            'showDate' => true,
        ],
    ];

    public function __construct(
        private EntityManagerInterface $em,
        private Pdf $pdf,
        private Environment $twig,
    ) {}

    public function getLayout(): array
    {
        $setting = $this->em->getRepository(Setting::class)->findOneBy(['key' => 'pdf.locationExport']);
        if ($setting) {
            return array_replace_recursive(self::DEFAULT_LAYOUT, $setting->getValue());
        }
        return self::DEFAULT_LAYOUT;
    }

    /**
     * Generate a PDF for the given locations.
     *
     * @param array $locations Array of location data arrays
     * @return string Binary PDF content
     */
    public function generateLocationsPdf(array $locations): string
    {
        $layout = $this->getLayout();
        $enabledColumns = array_filter($layout['columns'], fn($c) => $c['enabled'] ?? false);

        $html = $this->twig->render('pdf/locations.html.twig', [
            'layout' => $layout,
            'columns' => $enabledColumns,
            'locations' => $locations,
            'date' => (new \DateTime())->format('d F Y'),
            'count' => count($locations),
        ]);

        $margins = $layout['margins'];
        $options = [
            'page-size' => $layout['pageSize'] ?? 'A4',
            'orientation' => ucfirst($layout['orientation'] ?? 'landscape'),
            'margin-top' => $margins['top'] ?? 15,
            'margin-right' => $margins['right'] ?? 15,
            'margin-bottom' => $margins['bottom'] ?? 15,
            'margin-left' => $margins['left'] ?? 15,
        ];

        return $this->pdf->getOutputFromHtml($html, $options);
    }
}
