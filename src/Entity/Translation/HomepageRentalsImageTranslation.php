<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\HomepageRentalsImage;
use App\Trait\TranslationTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'homepage_rentals_image_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_HP_RENT_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class HomepageRentalsImageTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: HomepageRentalsImage::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?HomepageRentalsImage $translatable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $text = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $v): static
    {
        $this->text = $v;

        return $this;
    }
}
