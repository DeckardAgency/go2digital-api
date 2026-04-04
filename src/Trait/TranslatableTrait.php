<?php

declare(strict_types=1);

namespace App\Trait;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Use on the main entity (e.g., BlogPost).
 * Requires the entity to define:
 *   #[ORM\OneToMany(targetEntity: BlogPostTranslation::class, mappedBy: 'translatable', cascade: ['persist', 'remove'], orphanRemoval: true)]
 *   private Collection $translations;.
 *
 * And initialize in constructor: $this->translations = new ArrayCollection();
 */
trait TranslatableTrait
{
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function translate(string $locale): ?object
    {
        foreach ($this->translations as $translation) {
            if ($translation->getLocale() === $locale) {
                return $translation;
            }
        }

        return null;
    }

    public function addTranslation(object $translation): static
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setTranslatable($this);
        }

        return $this;
    }

    public function removeTranslation(object $translation): static
    {
        $this->translations->removeElement($translation);

        return $this;
    }

    /**
     * Returns all available locales for this entity.
     *
     * @return string[]
     */
    public function getAvailableLocales(): array
    {
        return $this->translations->map(
            fn (object $t) => $t->getLocale()
        )->toArray();
    }
}
