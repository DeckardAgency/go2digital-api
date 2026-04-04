<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\EsgPageContent;
use App\Trait\TranslationTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'esg_page_content_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_ESG_PC_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class EsgPageContentTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: EsgPageContent::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?EsgPageContent $translatable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $heroLabel = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $introSmall = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $introLarge = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $downloadReportLabel = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getHeroLabel(): ?string
    {
        return $this->heroLabel;
    }

    public function setHeroLabel(?string $heroLabel): static
    {
        $this->heroLabel = $heroLabel;

        return $this;
    }

    public function getIntroSmall(): ?string
    {
        return $this->introSmall;
    }

    public function setIntroSmall(?string $introSmall): static
    {
        $this->introSmall = $introSmall;

        return $this;
    }

    public function getIntroLarge(): ?string
    {
        return $this->introLarge;
    }

    public function setIntroLarge(?string $introLarge): static
    {
        $this->introLarge = $introLarge;

        return $this;
    }

    public function getDownloadReportLabel(): ?string
    {
        return $this->downloadReportLabel;
    }

    public function setDownloadReportLabel(?string $downloadReportLabel): static
    {
        $this->downloadReportLabel = $downloadReportLabel;

        return $this;
    }
}
