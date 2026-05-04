<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Translation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class TranslationsController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * Return all translations for a locale as a nested object built from flat dotted keys.
     * Numeric path segments are reconstructed as array indices so shapes like
     * "homepage.interactiveDisplay.specs.0.label" → { specs: [ { label: ... } ] }.
     */
    #[Route('/api/translations/messages/{locale}', name: 'api_translations_messages', methods: ['GET'])]
    public function messages(string $locale): JsonResponse
    {
        $rows = $this->em->getRepository(Translation::class)->findBy(['locale' => $locale]);

        $tree = [];
        foreach ($rows as $row) {
            $this->setByPath($tree, (string) $row->getKey(), $row->getValue());
        }
        $this->normalizeArrays($tree);

        return new JsonResponse($tree, 200, [
            'Cache-Control' => 'public, max-age=60, must-revalidate',
        ]);
    }

    private function setByPath(array &$tree, string $dottedKey, string $value): void
    {
        $segments = explode('.', $dottedKey);
        $cursor = &$tree;
        $last = array_key_last($segments);

        foreach ($segments as $i => $segment) {
            $idx = ctype_digit($segment) ? (int) $segment : $segment;

            if ($i === $last) {
                $cursor[$idx] = $value;
                break;
            }

            if (!isset($cursor[$idx]) || !is_array($cursor[$idx])) {
                $cursor[$idx] = [];
            }
            $cursor = &$cursor[$idx];
        }
    }

    private function normalizeArrays(array &$node): void
    {
        foreach ($node as &$child) {
            if (is_array($child)) {
                $this->normalizeArrays($child);
            }
        }
        unset($child);

        if ([] === $node) {
            return;
        }

        $allInt = true;
        foreach (array_keys($node) as $k) {
            if (!is_int($k)) {
                $allInt = false;
                break;
            }
        }
        if ($allInt) {
            ksort($node);
            $node = array_values($node);
        }
    }
}
