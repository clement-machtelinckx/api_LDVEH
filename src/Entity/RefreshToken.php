<?php

namespace App\Entity;

use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RefreshTokenRepository::class)]
#[ORM\Table(name: 'refresh_token')]
#[ORM\Index(name: 'idx_user', columns: ['user_id'])]
#[ORM\Index(name: 'idx_expires_at', columns: ['expires_at'])]
class RefreshToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private ?string $tokenHash = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $revokedAt = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $replacedByTokenHash = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTokenHash(): ?string
    {
        return $this->tokenHash;
    }

    public function setTokenHash(string $tokenHash): static
    {
        $this->tokenHash = $tokenHash;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRevokedAt(): ?\DateTimeImmutable
    {
        return $this->revokedAt;
    }

    public function setRevokedAt(?\DateTimeImmutable $revokedAt): static
    {
        $this->revokedAt = $revokedAt;

        return $this;
    }

    public function getReplacedByTokenHash(): ?string
    {
        return $this->replacedByTokenHash;
    }

    public function setReplacedByTokenHash(?string $replacedByTokenHash): static
    {
        $this->replacedByTokenHash = $replacedByTokenHash;

        return $this;
    }

    public function isExpired(\DateTimeImmutable $now): bool
    {
        return $this->expiresAt < $now;
    }

    public function isRevoked(): bool
    {
        return $this->revokedAt !== null;
    }

    public function isValid(\DateTimeImmutable $now): bool
    {
        return !$this->isExpired($now) && !$this->isRevoked();
    }
}
