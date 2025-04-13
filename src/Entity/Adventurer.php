<?php

namespace App\Entity;

use App\Repository\AdventurerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column]
    private ?int $Ability = null;

    #[ORM\Column]
    private ?int $Endurance = null;

    #[ORM\ManyToOne(inversedBy: 'adventurers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, FightHistory>
     */
    #[ORM\OneToMany(mappedBy: 'adventurer', targetEntity: FightHistory::class, orphanRemoval: true)]
    private Collection $fightHistories;
    
    public function __construct()
    {
        $this->fightHistories = new ArrayCollection();
    }

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
        return $this->Ability;
    }

    public function setAbility(int $Ability): static
    {
        $this->Ability = $Ability;

        return $this;
    }

    public function getEndurance(): ?int
    {
        return $this->Endurance;
    }

    public function setEndurance(int $Endurance): static
    {
        $this->Endurance = $Endurance;

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

    public function __toString(): string
    {
        return $this->AdventurerName; // Vous pouvez ajuster cela pour retourner une chaîne de caractères appropriée
    }

    /**
     * @return Collection<int, FightHistory>
     */
    public function getFightHistories(): Collection
    {
        return $this->fightHistories;
    }
    
    public function addFightHistory(FightHistory $history): static
    {
        if (!$this->fightHistories->contains($history)) {
            $this->fightHistories[] = $history;
            $history->setAdventurer($this);
        }
    
        return $this;
    }
    
    public function removeFightHistory(FightHistory $history): static
    {
        if ($this->fightHistories->removeElement($history)) {
            if ($history->getAdventurer() === $this) {
                $history->setAdventurer(null);
            }
        }
    
        return $this;
    }
}
