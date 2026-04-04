<?php

declare(strict_types=1);

namespace App\Entity\Translation;

use App\Entity\HomepageTextAnimation;
use App\Trait\TranslationTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'homepage_text_animation_translations')]
#[ORM\UniqueConstraint(name: 'UNIQ_HP_TANIM_TRANS_LOCALE', columns: ['translatable_id', 'locale'])]
class HomepageTextAnimationTranslation
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: HomepageTextAnimation::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', nullable: false, onDelete: 'CASCADE')]
    private ?HomepageTextAnimation $translatable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $word1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $word2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $word3 = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getWord1(): ?string
    {
        return $this->word1;
    }

    public function setWord1(?string $word1): static
    {
        $this->word1 = $word1;

        return $this;
    }

    public function getWord2(): ?string
    {
        return $this->word2;
    }

    public function setWord2(?string $word2): static
    {
        $this->word2 = $word2;

        return $this;
    }

    public function getWord3(): ?string
    {
        return $this->word3;
    }

    public function setWord3(?string $word3): static
    {
        $this->word3 = $word3;

        return $this;
    }
}
