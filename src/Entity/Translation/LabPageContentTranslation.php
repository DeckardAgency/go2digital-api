<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\LabPageContent;
use App\Trait\TranslationTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'lab_page_content_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_LAB_PC_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class LabPageContentTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: LabPageContent::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?LabPageContent $translatable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pageTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $breadcrumb = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $intro = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $filterAllLabel = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $noResultsText = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $viewAllLabel = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $viewProjectLabel = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getPageTitle(): ?string
    {
        return $this->pageTitle;
    }

    public function setPageTitle(?string $v): static
    {
        $this->pageTitle = $v;

        return $this;
    }

    public function getBreadcrumb(): ?string
    {
        return $this->breadcrumb;
    }

    public function setBreadcrumb(?string $v): static
    {
        $this->breadcrumb = $v;

        return $this;
    }

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(?string $v): static
    {
        $this->intro = $v;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $v): static
    {
        $this->title = $v;

        return $this;
    }

    public function getFilterAllLabel(): ?string
    {
        return $this->filterAllLabel;
    }

    public function setFilterAllLabel(?string $v): static
    {
        $this->filterAllLabel = $v;

        return $this;
    }

    public function getNoResultsText(): ?string
    {
        return $this->noResultsText;
    }

    public function setNoResultsText(?string $v): static
    {
        $this->noResultsText = $v;

        return $this;
    }

    public function getViewAllLabel(): ?string
    {
        return $this->viewAllLabel;
    }

    public function setViewAllLabel(?string $v): static
    {
        $this->viewAllLabel = $v;

        return $this;
    }

    public function getViewProjectLabel(): ?string
    {
        return $this->viewProjectLabel;
    }

    public function setViewProjectLabel(?string $v): static
    {
        $this->viewProjectLabel = $v;

        return $this;
    }
}
