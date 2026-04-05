<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\SeoMetadata;
use App\Trait\TranslationTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'seo_metadata_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_SEO_META_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class SeoMetadataTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: SeoMetadata::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?SeoMetadata $translatable = null;

    // --- Core Meta ---
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $keywords = null;

    // --- Open Graph ---
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ogTitle = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $ogDescription = null;

    // --- Twitter ---
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $twitterTitle = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $twitterDescription = null;

    public function getId(): ?Uuid { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $v): static { $this->title = $v; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $v): static { $this->description = $v; return $this; }

    public function getKeywords(): ?string { return $this->keywords; }
    public function setKeywords(?string $v): static { $this->keywords = $v; return $this; }

    public function getOgTitle(): ?string { return $this->ogTitle; }
    public function setOgTitle(?string $v): static { $this->ogTitle = $v; return $this; }

    public function getOgDescription(): ?string { return $this->ogDescription; }
    public function setOgDescription(?string $v): static { $this->ogDescription = $v; return $this; }

    public function getTwitterTitle(): ?string { return $this->twitterTitle; }
    public function setTwitterTitle(?string $v): static { $this->twitterTitle = $v; return $this; }

    public function getTwitterDescription(): ?string { return $this->twitterDescription; }
    public function setTwitterDescription(?string $v): static { $this->twitterDescription = $v; return $this; }
}
