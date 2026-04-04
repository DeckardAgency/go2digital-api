<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\ContactPageContent;
use App\Trait\TranslationTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'contact_page_content_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_CONTACT_PC_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class ContactPageContentTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: ContactPageContent::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?ContactPageContent $translatable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pageTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $batteryLine1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $batteryLine2 = null;

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

    public function getBatteryLine1(): ?string
    {
        return $this->batteryLine1;
    }

    public function setBatteryLine1(?string $v): static
    {
        $this->batteryLine1 = $v;

        return $this;
    }

    public function getBatteryLine2(): ?string
    {
        return $this->batteryLine2;
    }

    public function setBatteryLine2(?string $v): static
    {
        $this->batteryLine2 = $v;

        return $this;
    }
}
