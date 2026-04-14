<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Translation\BlogPageContentTranslation;
use App\Trait\TimestampableTrait;
use App\Trait\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'blog_page_content')]
#[ORM\HasLifecycleCallbacks]
class BlogPageContent
{
    use TimestampableTrait;
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\OneToOne(targetEntity: SeoMetadata::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?SeoMetadata $seoMetadata = null;

    /** @var Collection<int, BlogPageContentTranslation> */
    #[ORM\OneToMany(targetEntity: BlogPageContentTranslation::class, mappedBy: 'translatable', cascade: ['persist', 'remove'], orphanRemoval: true)]
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
