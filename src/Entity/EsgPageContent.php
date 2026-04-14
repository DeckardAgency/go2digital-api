<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Translation\EsgPageContentTranslation;
use App\Trait\TimestampableTrait;
use App\Trait\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'esg_page_content')]
#[ORM\HasLifecycleCallbacks]
class EsgPageContent
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
    private ?Media $video = null;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Media $mobileBg = null;

    #[ORM\OneToOne(targetEntity: SeoMetadata::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?SeoMetadata $seoMetadata = null;

    /** @var Collection<int, EsgPageContentTranslation> */
    #[ORM\OneToMany(targetEntity: EsgPageContentTranslation::class, mappedBy: 'translatable', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ApiProperty(writable: false)]
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getVideo(): ?Media
    {
        return $this->video;
    }

    public function setVideo(?Media $video): static
    {
        $this->video = $video;

        return $this;
    }

    public function getMobileBg(): ?Media
    {
        return $this->mobileBg;
    }

    public function setMobileBg(?Media $mobileBg): static
    {
        $this->mobileBg = $mobileBg;

        return $this;
    }

    public function getSeoMetadata(): ?SeoMetadata
    {
        return $this->seoMetadata;
    }

    public function setSeoMetadata(?SeoMetadata $seoMetadata): static
    {
        $this->seoMetadata = $seoMetadata;

        return $this;
    }
}
