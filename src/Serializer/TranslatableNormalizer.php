<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Media;
use App\Trait\TranslatableTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Handles translation resolution for all entities using TranslatableTrait.
 *
 * Public API (Nuxt): reads Accept-Language header, merges translated fields flat.
 * CMS API (Angular): with ?includeTranslations=true, returns all translations nested.
 */
class TranslatableNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'TRANSLATABLE_NORMALIZER_ALREADY_CALLED';
    private const DEFAULT_LOCALE = 'hr';
    private const SUPPORTED_LOCALES = ['hr', 'en'];

    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!is_object($data)) {
            return false;
        }

        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $this->isTranslatable($data);
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::ALREADY_CALLED] = true;

        /** @var array<string, mixed> $data */
        $data = $this->normalizer->normalize($object, $format, $context);

        if (!is_array($data)) {
            return $data;
        }

        $request = $this->requestStack->getCurrentRequest();
        $includeTranslations = $request?->query->getBoolean('includeTranslations', false) ?? false;

        // Remove raw translations collection from serialized output
        unset($data['translations']);

        // Expand image relation if it's an IRI string
        $this->expandMediaRelations($object, $data);

        if ($includeTranslations) {
            // CMS mode: nest all translations by locale
            $data['translations'] = $this->getAllTranslations($object, $format, $context);
        } else {
            // Public mode: merge the requested locale flat
            $locale = $this->resolveLocale();
            $translatedFields = $this->getTranslatedFields($object, $locale);
            $data = array_merge($data, $translatedFields);
            $data['locale'] = $locale;
        }

        return $data;
    }

    public function getSupportedTypes(?string $format): array
    {
        return ['object' => false];
    }

    private function isTranslatable(object $object): bool
    {
        return in_array(TranslatableTrait::class, $this->getTraits($object::class));
    }

    /**
     * @return string[]
     */
    private function getTraits(string $class): array
    {
        $traits = [];

        do {
            $traits = array_merge($traits, class_uses($class) ?: []);
        } while ($class = get_parent_class($class));

        return $traits;
    }

    private function resolveLocale(): string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return self::DEFAULT_LOCALE;
        }

        // Check Accept-Language header
        $acceptLanguage = $request->headers->get('Accept-Language', '');
        $preferred = $request->getPreferredLanguage(self::SUPPORTED_LOCALES);

        if (null !== $preferred && in_array($preferred, self::SUPPORTED_LOCALES)) {
            return $preferred;
        }

        return self::DEFAULT_LOCALE;
    }

    /**
     * Get translated fields for a specific locale.
     *
     * @return array<string, mixed>
     */
    private function getTranslatedFields(object $entity, string $locale): array
    {
        $translation = $entity->translate($locale);

        // Fallback to default locale
        if (null === $translation && self::DEFAULT_LOCALE !== $locale) {
            $translation = $entity->translate(self::DEFAULT_LOCALE);
        }

        if (null === $translation) {
            return [];
        }

        return $this->extractTranslationFields($translation);
    }

    /**
     * Get all translations organized by locale for CMS mode.
     *
     * @return array<string, array<string, mixed>>
     */
    private function getAllTranslations(object $entity, ?string $format, array $context): array
    {
        $result = [];

        foreach ($entity->getTranslations() as $translation) {
            $locale = $translation->getLocale();
            $result[$locale] = $this->extractTranslationFields($translation);
        }

        return $result;
    }

    /**
     * Expand Media relations from IRI strings to full objects.
     */
    private function expandMediaRelations(object $entity, array &$data): void
    {
        $reflection = new \ReflectionClass($entity);

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();
            if (!str_starts_with($name, 'get') || $method->getNumberOfRequiredParameters() > 0) continue;

            $returnType = $method->getReturnType();
            if (!$returnType instanceof \ReflectionNamedType) continue;
            if ($returnType->getName() !== Media::class) continue;

            $fieldName = lcfirst(substr($name, 3));
            $media = $method->invoke($entity);

            if ($media instanceof Media) {
                $data[$fieldName] = [
                    'id' => $media->getId()->toRfc4122(),
                    'path' => $media->getPath(),
                    'filename' => $media->getFilename(),
                    'originalFilename' => $media->getOriginalFilename(),
                    'mimeType' => $media->getMimeType(),
                    'width' => $media->getWidth(),
                    'height' => $media->getHeight(),
                    'thumbnails' => $media->getThumbnails(),
                    'focalX' => $media->getFocalX(),
                    'focalY' => $media->getFocalY(),
                ];
            }
        }
    }

    /**
     * Extract all non-system fields from a translation entity via getters.
     *
     * @return array<string, mixed>
     */
    private function extractTranslationFields(object $translation): array
    {
        $fields = [];
        $skipMethods = ['getId', 'getLocale', 'getTranslatable', 'getClass'];

        $reflection = new \ReflectionClass($translation);

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();

            if (!str_starts_with($name, 'get') || $method->getNumberOfRequiredParameters() > 0) {
                continue;
            }

            if (in_array($name, $skipMethods)) {
                continue;
            }

            // Convert getFieldName -> fieldName
            $fieldName = lcfirst(substr($name, 3));
            $fields[$fieldName] = $method->invoke($translation);
        }

        return $fields;
    }
}
