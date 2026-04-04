<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\BlogPageContent;
use App\Trait\TranslationTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'blog_page_content_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_BLOG_PC_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class BlogPageContentTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: BlogPageContent::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?BlogPageContent $translatable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pageTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $filterAllLabel = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $readMoreLabel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $noResultsTitle = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $noResultsText = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $viewAllLabel = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $allLoadedText = null;

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

    public function getReadMoreLabel(): ?string
    {
        return $this->readMoreLabel;
    }

    public function setReadMoreLabel(?string $v): static
    {
        $this->readMoreLabel = $v;

        return $this;
    }

    public function getNoResultsTitle(): ?string
    {
        return $this->noResultsTitle;
    }

    public function setNoResultsTitle(?string $v): static
    {
        $this->noResultsTitle = $v;

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

    public function getAllLoadedText(): ?string
    {
        return $this->allLoadedText;
    }

    public function setAllLoadedText(?string $v): static
    {
        $this->allLoadedText = $v;

        return $this;
    }
}
