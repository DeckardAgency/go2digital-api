<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\HomepageProduct;
use App\Trait\TranslationTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'homepage_product_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_HP_PROD_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class HomepageProductTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: HomepageProduct::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?HomepageProduct $translatable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $badge = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $specsTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $downloadLabel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $indicatorText = null;

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getBadge(): ?string
    {
        return $this->badge;
    }

    public function setBadge(?string $badge): static
    {
        $this->badge = $badge;

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

    public function getSpecsTitle(): ?string
    {
        return $this->specsTitle;
    }

    public function setSpecsTitle(?string $specsTitle): static
    {
        $this->specsTitle = $specsTitle;

        return $this;
    }

    public function getDownloadLabel(): ?string
    {
        return $this->downloadLabel;
    }

    public function setDownloadLabel(?string $downloadLabel): static
    {
        $this->downloadLabel = $downloadLabel;

        return $this;
    }

    public function getIndicatorText(): ?string
    {
        return $this->indicatorText;
    }

    public function setIndicatorText(?string $indicatorText): static
    {
        $this->indicatorText = $indicatorText;

        return $this;
    }
}
