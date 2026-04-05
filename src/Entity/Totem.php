<?php

declare(strict_types=1);

namespace App\Entity;

use App\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'totems')]
#[ORM\Index(columns: ['cdn_totem_id'], name: 'IDX_TOTEM_CDN_ID')]
#[ORM\HasLifecycleCallbacks]
class Totem
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column]
    private int $cdnTotemId = 0;

    #[ORM\ManyToOne(targetEntity: City::class, inversedBy: 'totems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?City $city = null;

    #[ORM\Column(length: 500)]
    private string $name = '';

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $nameEn = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $totemType = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $location = null;

    #[ORM\Column]
    private int $screens = 1;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $headerImage = null;

    #[ORM\Column]
    private bool $isInstalled = true;

    #[ORM\Column]
    private bool $isBigScreen = false;

    #[ORM\Column]
    private int $screenWidth = 0;

    #[ORM\Column]
    private int $screenHeight = 0;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $postbuyCategory = null;

    #[ORM\Column]
    private int $adDuration = 10;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEn = null;

    #[ORM\Column]
    private int $reach = 0;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $videoUrl = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $totemMotion = null;

    #[ORM\Column]
    private int $sortOrder = 0;

    #[ORM\Column]
    private bool $isPublished = true;

    #[ORM\OneToOne(targetEntity: SeoMetadata::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?SeoMetadata $seoMetadata = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $manualOverrides = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lastSyncedAt = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $images = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $floorPlans = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $totemScreens = null;

    public function getId(): ?Uuid { return $this->id; }
    public function getCdnTotemId(): int { return $this->cdnTotemId; }
    public function setCdnTotemId(int $v): static { $this->cdnTotemId = $v; return $this; }
    public function getCity(): ?City { return $this->city; }
    public function setCity(?City $v): static { $this->city = $v; return $this; }
    public function getName(): string { return $this->name; }
    public function setName(string $v): static { $this->name = $v; return $this; }
    public function getNameEn(): ?string { return $this->nameEn; }
    public function setNameEn(?string $v): static { $this->nameEn = $v; return $this; }
    public function getTotemType(): ?string { return $this->totemType; }
    public function setTotemType(?string $v): static { $this->totemType = $v; return $this; }
    public function getLocation(): ?array { return $this->location; }
    public function setLocation(?array $v): static { $this->location = $v; return $this; }
    public function getScreens(): int { return $this->screens; }
    public function setScreens(int $v): static { $this->screens = $v; return $this; }
    public function getHeaderImage(): ?string { return $this->headerImage; }
    public function setHeaderImage(?string $v): static { $this->headerImage = $v; return $this; }
    public function isInstalled(): bool { return $this->isInstalled; }
    public function setIsInstalled(bool $v): static { $this->isInstalled = $v; return $this; }
    public function isBigScreen(): bool { return $this->isBigScreen; }
    public function setIsBigScreen(bool $v): static { $this->isBigScreen = $v; return $this; }
    public function getScreenWidth(): int { return $this->screenWidth; }
    public function setScreenWidth(int $v): static { $this->screenWidth = $v; return $this; }
    public function getScreenHeight(): int { return $this->screenHeight; }
    public function setScreenHeight(int $v): static { $this->screenHeight = $v; return $this; }
    public function getPostbuyCategory(): ?string { return $this->postbuyCategory; }
    public function setPostbuyCategory(?string $v): static { $this->postbuyCategory = $v; return $this; }
    public function getAdDuration(): int { return $this->adDuration; }
    public function setAdDuration(int $v): static { $this->adDuration = $v; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $v): static { $this->description = $v; return $this; }
    public function getDescriptionEn(): ?string { return $this->descriptionEn; }
    public function setDescriptionEn(?string $v): static { $this->descriptionEn = $v; return $this; }
    public function getReach(): int { return $this->reach; }
    public function setReach(int $v): static { $this->reach = $v; return $this; }
    public function getVideoUrl(): ?string { return $this->videoUrl; }
    public function setVideoUrl(?string $v): static { $this->videoUrl = $v; return $this; }
    public function getTotemMotion(): ?string { return $this->totemMotion; }
    public function setTotemMotion(?string $v): static { $this->totemMotion = $v; return $this; }
    public function getSortOrder(): int { return $this->sortOrder; }
    public function setSortOrder(int $v): static { $this->sortOrder = $v; return $this; }
    public function isPublished(): bool { return $this->isPublished; }
    public function setIsPublished(bool $v): static { $this->isPublished = $v; return $this; }
    public function getSeoMetadata(): ?SeoMetadata { return $this->seoMetadata; }
    public function setSeoMetadata(?SeoMetadata $v): static { $this->seoMetadata = $v; return $this; }

    public function getManualOverrides(): ?array { return $this->manualOverrides; }
    public function setManualOverrides(?array $v): static { $this->manualOverrides = $v; return $this; }
    public function getLastSyncedAt(): ?\DateTimeImmutable { return $this->lastSyncedAt; }
    public function setLastSyncedAt(?\DateTimeImmutable $v): static { $this->lastSyncedAt = $v; return $this; }
    public function getImages(): ?array { return $this->images; }
    public function setImages(?array $v): static { $this->images = $v; return $this; }
    public function getFloorPlans(): ?array { return $this->floorPlans; }
    public function setFloorPlans(?array $v): static { $this->floorPlans = $v; return $this; }
    public function getTotemScreens(): ?array { return $this->totemScreens; }
    public function setTotemScreens(?array $v): static { $this->totemScreens = $v; return $this; }

    public function isManuallyOverridden(string $field): bool
    {
        return isset($this->manualOverrides[$field]);
    }

    public function markManualOverride(string $field): void
    {
        $overrides = $this->manualOverrides ?? [];
        $overrides[$field] = true;
        $this->manualOverrides = $overrides;
    }
}
