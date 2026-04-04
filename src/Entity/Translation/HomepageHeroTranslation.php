<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\HomepageHero;
use App\Trait\TranslationTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'homepage_hero_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_HP_HERO_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class HomepageHeroTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: HomepageHero::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?HomepageHero $translatable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titleLine1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titleLine2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $kicker = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $heading = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $scrollDownLabel = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTitleLine1(): ?string
    {
        return $this->titleLine1;
    }

    public function setTitleLine1(?string $titleLine1): static
    {
        $this->titleLine1 = $titleLine1;

        return $this;
    }

    public function getTitleLine2(): ?string
    {
        return $this->titleLine2;
    }

    public function setTitleLine2(?string $titleLine2): static
    {
        $this->titleLine2 = $titleLine2;

        return $this;
    }

    public function getKicker(): ?string
    {
        return $this->kicker;
    }

    public function setKicker(?string $kicker): static
    {
        $this->kicker = $kicker;

        return $this;
    }

    public function getHeading(): ?string
    {
        return $this->heading;
    }

    public function setHeading(?string $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getScrollDownLabel(): ?string
    {
        return $this->scrollDownLabel;
    }

    public function setScrollDownLabel(?string $scrollDownLabel): static
    {
        $this->scrollDownLabel = $scrollDownLabel;

        return $this;
    }
}
