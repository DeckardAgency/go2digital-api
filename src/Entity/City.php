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
#[ORM\Table(name: 'cities')]
#[ORM\HasLifecycleCallbacks]
class City
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column]
    private int $cdnCityId = 0;

    #[ORM\Column(length: 255)]
    private string $name = '';

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $location = null;

    #[ORM\Column]
    private int $sortOrder = 0;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lastSyncedAt = null;

    /** @var Collection<int, Totem> */
    #[ORM\OneToMany(targetEntity: Totem::class, mappedBy: 'city', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['sortOrder' => 'ASC'])]
    private Collection $totems;

    public function __construct()
    {
        $this->totems = new ArrayCollection();
    }

    public function getId(): ?Uuid { return $this->id; }
    public function getCdnCityId(): int { return $this->cdnCityId; }
    public function setCdnCityId(int $v): static { $this->cdnCityId = $v; return $this; }
    public function getName(): string { return $this->name; }
    public function setName(string $v): static { $this->name = $v; return $this; }
    public function getLocation(): ?array { return $this->location; }
    public function setLocation(?array $v): static { $this->location = $v; return $this; }
    public function getSortOrder(): int { return $this->sortOrder; }
    public function setSortOrder(int $v): static { $this->sortOrder = $v; return $this; }
    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $v): static { $this->isActive = $v; return $this; }
    public function getLastSyncedAt(): ?\DateTimeImmutable { return $this->lastSyncedAt; }
    public function setLastSyncedAt(?\DateTimeImmutable $v): static { $this->lastSyncedAt = $v; return $this; }

    /** @return Collection<int, Totem> */
    public function getTotems(): Collection { return $this->totems; }
    public function addTotem(Totem $t): static { if (!$this->totems->contains($t)) { $this->totems->add($t); $t->setCity($this); } return $this; }
}
