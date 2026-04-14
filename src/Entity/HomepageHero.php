<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Translation\HomepageHeroTranslation;
use App\Trait\TimestampableTrait;
use App\Trait\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'homepage_hero')]
#[ORM\HasLifecycleCallbacks]
class HomepageHero
{
    use TimestampableTrait;
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Media $video = null;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Media $mobileVideo = null;

    /** @var Collection<int, HomepageHeroTranslation> */
    #[ORM\OneToMany(targetEntity: HomepageHeroTranslation::class, mappedBy: 'translatable', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ApiProperty(writable: false)]
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getVideo(): ?Media
    {
        return $this->video;
    }

    public function setVideo(?Media $video): static
    {
        $this->video = $video;

        return $this;
    }

    public function getMobileVideo(): ?Media
    {
        return $this->mobileVideo;
    }

    public function setMobileVideo(?Media $mobileVideo): static
    {
        $this->mobileVideo = $mobileVideo;

        return $this;
    }
}
