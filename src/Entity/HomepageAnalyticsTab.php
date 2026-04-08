<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Translation\HomepageAnalyticsTabTranslation;
use App\Trait\TimestampableTrait;
use App\Trait\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: "is_granted('ROLE_EDITOR')"),
        new Patch(security: "is_granted('ROLE_EDITOR')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
    order: ['sortOrder' => 'ASC'],
    paginationEnabled: false,
)]
#[ORM\Entity]
#[ORM\Table(name: 'homepage_analytics_tabs')]
#[ORM\HasLifecycleCallbacks]
class HomepageAnalyticsTab
{
    use TimestampableTrait;
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 50)]
    private string $curveType = 'rising';

    /** @var string[]|null */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $yLabels = null;

    #[ORM\Column]
    private int $sortOrder = 0;

    /** @var Collection<int, HomepageAnalyticsTabTranslation> */
    #[ORM\OneToMany(targetEntity: HomepageAnalyticsTabTranslation::class, mappedBy: 'translatable', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCurveType(): string
    {
        return $this->curveType;
    }

    public function setCurveType(string $curveType): static
    {
        $this->curveType = $curveType;

        return $this;
    }

    public function getYLabels(): ?array
    {
        return $this->yLabels;
    }

    public function setYLabels(?array $yLabels): static
    {
        $this->yLabels = $yLabels;

        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }
}
