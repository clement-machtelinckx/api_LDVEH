<?php

namespace App\Entity;

use App\Repository\FightHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FightHistoryRepository::class)]
class FightHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'fightHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Adventurer $adventurer = null;

    #[ORM\ManyToOne]
    private ?Monster $monster = null;

    #[ORM\Column(nullable: true)]
    private ?bool $victory = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdventurer(): ?Adventurer
    {
        return $this->adventurer;
    }

    public function setAdventurer(?Adventurer $adventurer): static
    {
        $this->adventurer = $adventurer;

        return $this;
    }

    public function getMonster(): ?Monster
    {
        return $this->monster;
    }

    public function setMonster(?Monster $monster): static
    {
        $this->monster = $monster;

        return $this;
    }

    public function isVictory(): ?bool
    {
        return $this->victory;
    }

    public function setVictory(?bool $victory): static
    {
        $this->victory = $victory;

        return $this;
    }
}
