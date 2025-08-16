<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\AdventurerRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: AdventurerRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['adventurer:read']],
    denormalizationContext: ['groups' => ['adventurer:write']],
    operations: [
        new Get(
            uriTemplate: '/adventurers/{id}',
            name: 'get_adventurer',
            requirements: ['id' => '\d+'],
            security: "is_granted('ROLE_ADMIN') or object.getUser() == user",
            securityMessage: "Accès refusé."
        ),
    ]
)]
class Adventurer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['adventurer:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['adventurer:read', 'adventurer:write'])]
    private ?string $AdventurerName = null;

    #[ORM\Column]
    #[Groups(['adventurer:read', 'adventurer:write'])]
    private ?int $Ability = null;

    #[ORM\Column]
    #[Groups(['adventurer:read', 'adventurer:write'])]
    private ?int $Endurance = null;

    #[ORM\ManyToOne(inversedBy: 'adventurers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, FightHistory>
     */
    #[ORM\OneToMany(mappedBy: 'adventurer', targetEntity: FightHistory::class, orphanRemoval: true)]
    private Collection $fightHistories;

    #[ORM\OneToOne(mappedBy: 'adventurer', cascade: ['persist', 'remove'])]
    #[Groups(['adventurer:read', 'adventurer:write'])]
    // #[MaxDepth(1)]
    private ?Adventure $adventure = null;
    
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

    public function getAdventure(): ?Adventure
    {
        return $this->adventure;
    }

    public function setAdventure(Adventure $adventure): static
    {
        // set the owning side of the relation if necessary
        if ($adventure->getAdventurer() !== $this) {
            $adventure->setAdventurer($this);
        }

        $this->adventure = $adventure;

        return $this;
    }
}
