<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\HomepageCustomImage;
use App\Trait\TranslationTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'homepage_custom_image_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_HP_CIMG_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class HomepageCustomImageTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: HomepageCustomImage::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?HomepageCustomImage $translatable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $v): static
    {
        $this->alt = $v;

        return $this;
    }
}
