<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Translation\SeoMetadataTranslation;
use App\Trait\TimestampableTrait;
use App\Trait\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ],
)]
#[ORM\Entity]
#[ORM\Table(name: 'seo_metadata')]
#[ORM\HasLifecycleCallbacks]
class SeoMetadata
{
    use TimestampableTrait;
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Media $ogImage = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $ogType = 'website';

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $twitterCard = 'summary_large_image';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $canonicalUrl = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $robots = 'index, follow';

    #[ORM\Column(nullable: true)]
    private ?bool $noIndex = false;

    #[ORM\Column(nullable: true)]
    private ?bool $noFollow = false;

    /** @var Collection<int, SeoMetadataTranslation> */
    #[ORM\OneToMany(targetEntity: SeoMetadataTranslation::class, mappedBy: 'translatable', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ApiProperty(writable: false)]
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?Uuid { return $this->id; }

    public function getOgImage(): ?Media { return $this->ogImage; }
    public function setOgImage(?Media $v): static { $this->ogImage = $v; return $this; }

    public function getOgType(): ?string { return $this->ogType; }
    public function setOgType(?string $v): static { $this->ogType = $v; return $this; }

    public function getTwitterCard(): ?string { return $this->twitterCard; }
    public function setTwitterCard(?string $v): static { $this->twitterCard = $v; return $this; }

    public function getCanonicalUrl(): ?string { return $this->canonicalUrl; }
    public function setCanonicalUrl(?string $v): static { $this->canonicalUrl = $v; return $this; }

    public function getRobots(): ?string { return $this->robots; }
    public function setRobots(?string $v): static { $this->robots = $v; return $this; }

    public function isNoIndex(): ?bool { return $this->noIndex; }
    public function setNoIndex(?bool $v): static { $this->noIndex = $v; return $this; }

    public function isNoFollow(): ?bool { return $this->noFollow; }
    public function setNoFollow(?bool $v): static { $this->noFollow = $v; return $this; }

    /**
     * Build the robots directive string from noIndex/noFollow flags.
     */
    public function getRobotsDirective(): string
    {
        $parts = [];
        $parts[] = $this->noIndex ? 'noindex' : 'index';
        $parts[] = $this->noFollow ? 'nofollow' : 'follow';

        return implode(', ', $parts);
    }
}
