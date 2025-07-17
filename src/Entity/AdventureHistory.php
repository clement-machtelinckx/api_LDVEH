<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\AdventureHistoryRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AdventureHistoryRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['history:read']],
    order: ['finishAt' => 'DESC']
)]
class AdventureHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['history:read'])]
    private ?string $bookTitle = null;

    #[ORM\Column(length: 255)]
    #[Groups(['history:read'])]
    private ?string $adventurerName = null;

    #[ORM\Column]
    #[Groups(['history:read'])]
    private ?\DateTimeImmutable $finishAt = null;

    #[ORM\ManyToOne(inversedBy: 'adventureHistories')]
    private ?Book $book = null;

    #[ORM\ManyToOne(inversedBy: 'adventureHistories')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getBookTitle(): ?string
    {
        return $this->bookTitle;
    }

    public function setBookTitle(?string $bookTitle): static
    {
        $this->bookTitle = $bookTitle;

        return $this;
    }

    public function getAdventurerName(): ?string
    {
        return $this->adventurerName;
    }

    public function setAdventurerName(string $adventurerName): static
    {
        $this->adventurerName = $adventurerName;

        return $this;
    }

    public function getFinishAt(): ?\DateTimeImmutable
    {
        return $this->finishAt;
    }

    public function setFinishAt(\DateTimeImmutable $finishAt): static
    {
        $this->finishAt = $finishAt;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
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
}
