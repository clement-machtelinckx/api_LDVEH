<?php

namespace App\Entity;

use App\Repository\AdventurerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdventurerRepository::class)]
class Adventurer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $AdventurerName = null;

    #[ORM\Column(nullable: true)]
    private ?int $ability = null;

    #[ORM\Column(nullable: true)]
    private ?int $endurance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getAdventurerName(): ?string
    {
        return $this->AdventurerName;
    }

    public function setAdventurerName(string $AdventurerName): static
    {
        $this->AdventurerName = $AdventurerName;

        return $this;
    }

    public function getAbility(): ?int
    {
        return $this->ability;
    }

    public function setAbility(?int $ability): static
    {
        $this->ability = $ability;

        return $this;
    }

    public function getEndurance(): ?int
    {
        return $this->endurance;
    }

    public function setEndurance(?int $endurance): static
    {
        $this->endurance = $endurance;

        return $this;
    }
}
