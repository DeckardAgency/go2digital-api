<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * API Platform state processor that handles nested translations
 * payload: { translations: { hr: { title: "...", ... }, en: { ... } }, ... }
 *
 * Works with any entity that uses TranslatableTrait.
 */
class TranslatableProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private EntityManagerInterface $em,
        private RequestStack $requestStack,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // Check if entity uses TranslatableTrait
        if (!method_exists($data, 'getTranslations') || !method_exists($data, 'translate')) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        // Remove ALL non-persisted translation objects added by API Platform's
        // deserializer. Our applyTranslations handles everything from the raw payload.
        $toRemove = [];
        foreach ($data->getTranslations() as $translation) {
            if (!$translation->getId()) {
                $toRemove[] = $translation;
            }
        }
        foreach ($toRemove as $t) {
            $data->getTranslations()->removeElement($t);
            if ($this->em->contains($t)) {
                $this->em->detach($t);
            }
        }

        // Get the raw request body for translations
        $request = $this->requestStack->getCurrentRequest();
        $body = $request ? json_decode($request->getContent(), true) : null;

        if ($body && isset($body['translations']) && is_array($body['translations'])) {
            $this->applyTranslations($data, $body['translations']);
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

    private function applyTranslations(object $entity, array $translations): void
    {
        // Discover translation class from the OneToMany mapping
        $translationClass = $this->getTranslationClass($entity);
        if (!$translationClass) return;

        foreach ($translations as $locale => $fields) {
            if (!is_array($fields)) continue;

            $translation = $entity->translate($locale);

            if (!$translation) {
                $translation = new $translationClass();
                $translation->setLocale($locale);
                $entity->addTranslation($translation);
            }

            foreach ($fields as $field => $value) {
                $setter = 'set' . ucfirst($field);
                if (method_exists($translation, $setter)) {
                    $translation->$setter($value);
                }
            }
        }
    }

    private function getTranslationClass(object $entity): ?string
    {
        $metadata = $this->em->getClassMetadata(get_class($entity));

        foreach ($metadata->associationMappings as $mapping) {
            if (isset($mapping['targetEntity']) && str_contains($mapping['targetEntity'], 'Translation')) {
                return $mapping['targetEntity'];
            }
        }

        return null;
    }
}
