<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\HomepageCustomSolution;
use App\Trait\TranslationTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'homepage_custom_solution_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_HP_CSOL_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class HomepageCustomSolutionTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: HomepageCustomSolution::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?HomepageCustomSolution $translatable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $indicator = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $block1 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $block2 = null;

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

    public function getBlock1(): ?string
    {
        return $this->block1;
    }

    public function setBlock1(?string $block1): static
    {
        $this->block1 = $block1;

        return $this;
    }

    public function getBlock2(): ?string
    {
        return $this->block2;
    }

    public function setBlock2(?string $block2): static
    {
        $this->block2 = $block2;

        return $this;
    }
}
