<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Translation\HomepageCustomImageTranslation;
use App\Trait\TimestampableTrait;
use App\Trait\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'homepage_custom_image')]
#[ORM\HasLifecycleCallbacks]
class HomepageCustomImage
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
    private ?Media $desktopImage = null;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Media $mobileImage = null;

    /** @var Collection<int, HomepageCustomImageTranslation> */
    #[ORM\OneToMany(targetEntity: HomepageCustomImageTranslation::class, mappedBy: 'translatable', cascade: ['persist', 'remove'], orphanRemoval: true)]
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

    public function getDesktopImage(): ?Media
    {
        return $this->desktopImage;
    }

    public function setDesktopImage(?Media $desktopImage): static
    {
        $this->desktopImage = $desktopImage;

        return $this;
    }

    public function getMobileImage(): ?Media
    {
        return $this->mobileImage;
    }

    public function setMobileImage(?Media $mobileImage): static
    {
        $this->mobileImage = $mobileImage;

        return $this;
    }
}
