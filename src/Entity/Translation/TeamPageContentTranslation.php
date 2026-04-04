<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\TeamPageContent;
use App\Trait\TranslationTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'team_page_content_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_TEAM_PC_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class TeamPageContentTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: TeamPageContent::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?TeamPageContent $translatable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pageTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $intro = null;

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

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(?string $v): static
    {
        $this->intro = $v;

        return $this;
    }
}
