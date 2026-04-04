<?php

declare(strict_types=1);

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;

/**
 * Use on translation entities (e.g., BlogPostTranslation).
 * Requires the entity to define:
 *   #[ORM\ManyToOne(targetEntity: BlogPost::class, inversedBy: 'translations')]
 *   #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
 *   private ?BlogPost $translatable = null;.
 */
trait TranslationTrait
{
    #[ORM\Column(length: 5)]
    private ?string $locale = null;

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTranslatable(): ?object
    {
        return $this->translatable;
    }

    public function setTranslatable(?object $translatable): static
    {
        $this->translatable = $translatable;

        return $this;
    }
}
