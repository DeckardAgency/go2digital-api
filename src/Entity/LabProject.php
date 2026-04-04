<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Translation\LabProjectTranslation;
use App\Enum\ContentStatus;
use App\Trait\TimestampableTrait;
use App\Trait\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: "is_granted('ROLE_EDITOR')"),
        new Patch(security: "is_granted('ROLE_EDITOR')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
    order: ['createdAt' => 'DESC'],
)]
#[ApiFilter(SearchFilter::class, properties: ['slug' => 'exact', 'categories.slug' => 'exact', 'status' => 'exact'])]
#[ApiFilter(BooleanFilter::class, properties: ['featured'])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt'])]
#[ORM\Entity]
#[ORM\Table(name: 'lab_projects')]
#[ORM\Index(columns: ['status'], name: 'IDX_LAB_STATUS')]
#[ORM\Index(columns: ['featured'], name: 'IDX_LAB_FEATURED')]
#[ORM\HasLifecycleCallbacks]
class LabProject
{
    use TimestampableTrait;
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    private ?string $slug = null;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Media $image = null;

    #[ORM\Column]
    private bool $featured = false;

    #[ORM\Column(length: 20, enumType: ContentStatus::class)]
    private ContentStatus $status = ContentStatus::Draft;

    #[ORM\OneToOne(targetEntity: SeoMetadata::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?SeoMetadata $seoMetadata = null;

    /** @var Collection<int, LabProjectTranslation> */
    #[ORM\OneToMany(targetEntity: LabProjectTranslation::class, mappedBy: 'translatable', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $translations;

    /** @var Collection<int, LabCategory> */
    #[ORM\ManyToMany(targetEntity: LabCategory::class, inversedBy: 'projects')]
    #[ORM\JoinTable(name: 'lab_project_categories')]
    private Collection $categories;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function setImage(?Media $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): static
    {
        $this->featured = $featured;

        return $this;
    }

    public function getStatus(): ContentStatus
    {
        return $this->status;
    }

    public function setStatus(ContentStatus $status): static
    {
        $this->status = $status;

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

    /** @return Collection<int, LabCategory> */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(LabCategory $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(LabCategory $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
