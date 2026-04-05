<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'sync_logs')]
class SyncLog
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column]
    private bool $success = true;

    #[ORM\Column(type: Types::JSON)]
    private array $report = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $error = null;

    #[ORM\Column(type: Types::FLOAT)]
    private float $durationMs = 0;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $triggeredBy = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid { return $this->id; }
    public function isSuccess(): bool { return $this->success; }
    public function setSuccess(bool $v): static { $this->success = $v; return $this; }
    public function getReport(): array { return $this->report; }
    public function setReport(array $v): static { $this->report = $v; return $this; }
    public function getError(): ?string { return $this->error; }
    public function setError(?string $v): static { $this->error = $v; return $this; }
    public function getDurationMs(): float { return $this->durationMs; }
    public function setDurationMs(float $v): static { $this->durationMs = $v; return $this; }
    public function getTriggeredBy(): ?string { return $this->triggeredBy; }
    public function setTriggeredBy(?string $v): static { $this->triggeredBy = $v; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
