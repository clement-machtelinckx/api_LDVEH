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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
    // Limites d'inventaire (règles Loup Solitaire)
    public const MAX_WEAPONS = 2;
    public const MAX_BACKPACK = 8;
    public const MAX_GOLD = 50;
    public const MAX_SKILLS = 5;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['adventurer:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['adventurer:read', 'adventurer:write'])]
    #[Assert\NotBlank]
    private ?string $AdventurerName = null;

    #[ORM\Column]
    #[Groups(['adventurer:read', 'adventurer:write'])]
    #[Assert\NotNull]
    #[Assert\Range(min: 10, max: 19, notInRangeMessage: 'Habileté doit être entre {{ min }} et {{ max }} (tirage 0-9 + 10).')]
    private ?int $Ability = null;

    #[ORM\Column]
    #[Groups(['adventurer:read', 'adventurer:write'])]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
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

    /**
     * @var Collection<int, AdventurerEquipment>
     */
    #[ORM\OneToMany(mappedBy: 'adventurer', targetEntity: AdventurerEquipment::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $adventurerEquipments;

    /**
     * @var Collection<int, Skill>
     */
    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'adventurers')]
    private Collection $skills;

    #[ORM\Column(options: ['default' => 0])]
    #[Assert\Range(min: 0, max: 50, notInRangeMessage: 'Or : entre {{ min }} et {{ max }} couronnes.')]
    private int $gold = 0;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Range(min: 20, max: 29, notInRangeMessage: 'Endurance max doit être entre {{ min }} et {{ max }} (tirage 0-9 + 20).')]
    private ?int $maxEndurance = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $masteredWeaponSlug = null;

    public function __construct()
    {
        $this->fightHistories = new ArrayCollection();
        $this->adventurerEquipments = new ArrayCollection();
        $this->skills = new ArrayCollection();
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
        return $this->AdventurerName;
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

    /**
     * @return Collection<int, AdventurerEquipment>
     */
    public function getAdventurerEquipments(): Collection
    {
        return $this->adventurerEquipments;
    }

    public function findAdventurerEquipment(Equipment $equipment): ?AdventurerEquipment
    {
        foreach ($this->adventurerEquipments as $ae) {
            if ($ae->getEquipment() === $equipment) {
                return $ae;
            }
        }

        return null;
    }

    public function addEquipment(Equipment $equipment, int $quantity = 1): static
    {
        $ae = $this->findAdventurerEquipment($equipment);
        if ($ae) {
            $ae->setQuantity($ae->getQuantity() + $quantity);
        } else {
            $ae = new AdventurerEquipment();
            $ae->setAdventurer($this);
            $ae->setEquipment($equipment);
            $ae->setQuantity($quantity);
            $this->adventurerEquipments->add($ae);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment, int $quantity = 1): static
    {
        $ae = $this->findAdventurerEquipment($equipment);
        if ($ae) {
            $newQty = $ae->getQuantity() - $quantity;
            if ($newQty <= 0) {
                $this->adventurerEquipments->removeElement($ae);
            } else {
                $ae->setQuantity($newQty);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): static
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
            $skill->addAdventurer($this);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): static
    {
        if ($this->skills->removeElement($skill)) {
            $skill->removeAdventurer($this);
        }

        return $this;
    }

    public function getGold(): int
    {
        return $this->gold;
    }

    public function setGold(int $gold): static
    {
        $this->gold = min(self::MAX_GOLD, max(0, $gold));

        return $this;
    }

    public function addGold(int $amount): static
    {
        $this->gold = min(self::MAX_GOLD, max(0, $this->gold + $amount));

        return $this;
    }

    public function getMaxEndurance(): ?int
    {
        return $this->maxEndurance;
    }

    public function setMaxEndurance(int $maxEndurance): static
    {
        $this->maxEndurance = $maxEndurance;

        return $this;
    }

    /**
     * Endurance max effective = base + bonus des équipements portés.
     */
    public function getEffectiveMaxEndurance(): int
    {
        $bonus = 0;
        foreach ($this->adventurerEquipments as $ae) {
            $bonus += $ae->getEquipment()->getEnduranceBonus() * $ae->getQuantity();
        }

        return ($this->maxEndurance ?? 0) + $bonus;
    }

    public function getMasteredWeaponSlug(): ?string
    {
        return $this->masteredWeaponSlug;
    }

    public function setMasteredWeaponSlug(?string $masteredWeaponSlug): static
    {
        $this->masteredWeaponSlug = $masteredWeaponSlug;

        return $this;
    }

    #[Assert\Callback]
    public function validateEndurance(ExecutionContextInterface $context): void
    {
        $effectiveMax = $this->getEffectiveMaxEndurance();
        if ($this->Endurance !== null && $this->maxEndurance !== null && $this->Endurance > $effectiveMax) {
            $context->buildViolation('L\'endurance ({{ current }}) ne peut pas dépasser l\'endurance max ({{ max }}).')
                ->setParameter('{{ current }}', (string) $this->Endurance)
                ->setParameter('{{ max }}', (string) $effectiveMax)
                ->atPath('Endurance')
                ->addViolation();
        }
    }

    /**
     * Vérifie si l'aventurier possède un équipement ou skill par son slug.
     */
    public function hasSlug(string $slug): bool
    {
        foreach ($this->adventurerEquipments as $ae) {
            if ($ae->getEquipment()->getSlug() === $slug) {
                return true;
            }
        }
        foreach ($this->skills as $sk) {
            if ($sk->getSlug() === $slug) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si l'aventurier possède au moins une arme.
     */
    public function hasWeapon(): bool
    {
        foreach ($this->adventurerEquipments as $ae) {
            if ($ae->getEquipment()->getType() === \App\Enum\EquipmentType::Weapon) {
                return true;
            }
        }

        return false;
    }
}
