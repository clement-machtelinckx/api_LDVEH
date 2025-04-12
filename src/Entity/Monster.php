<?php

namespace App\Entity;

use App\Repository\MonsterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MonsterRepository::class)]
class Monster
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $monsterName = null;

    #[ORM\Column]
    private ?int $ability = null;

    #[ORM\Column]
    private ?int $endurance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonsterName(): ?string
    {
        return $this->monsterName;
    }

    public function setMonsterName(string $monsterName): static
    {
        $this->monsterName = $monsterName;

        return $this;
    }

    public function getAbility(): ?int
    {
        return $this->ability;
    }

    public function setAbility(int $ability): static
    {
        $this->ability = $ability;

        return $this;
    }

    public function getEndurance(): ?int
    {
        return $this->endurance;
    }

    public function setEndurance(int $endurance): static
    {
        $this->endurance = $endurance;

        return $this;
    }
}
