<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\HomepageAnalytics;
use App\Trait\TranslationTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'homepage_analytics_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_HP_ANALYTICS_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class HomepageAnalyticsTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: HomepageAnalytics::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?HomepageAnalytics $translatable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $indicator = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $description = null;

    public function getId(): ?Uuid { return $this->id; }

    public function getIndicator(): ?string { return $this->indicator; }
    public function setIndicator(?string $v): static { $this->indicator = $v; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $v): static { $this->title = $v; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $v): static { $this->description = $v; return $this; }
}
