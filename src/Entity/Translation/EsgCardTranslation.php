<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\EsgCard;
use App\Trait\TranslationTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'esg_card_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_ESG_CARD_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class EsgCardTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: EsgCard::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?EsgCard $translatable = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $text = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }
}
