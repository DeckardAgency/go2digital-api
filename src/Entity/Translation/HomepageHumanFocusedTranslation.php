<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\HomepageHumanFocused;
use App\Trait\TranslationTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'homepage_human_focused_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_HP_HF_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class HomepageHumanFocusedTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: HomepageHumanFocused::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?HomepageHumanFocused $translatable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $indicator = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $blockLeft = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $blockRight = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getIndicator(): ?string
    {
        return $this->indicator;
    }

    public function setIndicator(?string $indicator): static
    {
        $this->indicator = $indicator;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getBlockLeft(): ?string
    {
        return $this->blockLeft;
    }

    public function setBlockLeft(?string $blockLeft): static
    {
        $this->blockLeft = $blockLeft;

        return $this;
    }

    public function getBlockRight(): ?string
    {
        return $this->blockRight;
    }

    public function setBlockRight(?string $blockRight): static
    {
        $this->blockRight = $blockRight;

        return $this;
    }
}
