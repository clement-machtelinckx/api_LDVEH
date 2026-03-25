<?php

namespace App\Entity;

use App\Enum\EquipmentType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'adventurer_equipment_unique', columns: ['adventurer_id', 'equipment_id'])]
class AdventurerEquipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Adventurer::class, inversedBy: 'adventurerEquipments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Adventurer $adventurer = null;

    #[ORM\ManyToOne(targetEntity: Equipment::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipment $equipment = null;

    #[ORM\Column(options: ['default' => 1])]
    #[Assert\Positive]
    private int $quantity = 1;

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

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): static
    {
        $this->equipment = $equipment;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    #[Assert\Callback]
    public function validateInventoryLimits(ExecutionContextInterface $context): void
    {
        if (!$this->adventurer || !$this->equipment) {
            return;
        }

        $type = $this->equipment->getType();

        // Armes : max 2
        if ($type === EquipmentType::Weapon) {
            $weaponCount = 0;
            foreach ($this->adventurer->getAdventurerEquipments() as $ae) {
                if ($ae === $this) {
                    continue;
                }
                if ($ae->getEquipment()->getType() === EquipmentType::Weapon) {
                    $weaponCount += $ae->getQuantity();
                }
            }
            if ($weaponCount + $this->quantity > Adventurer::MAX_WEAPONS) {
                $context->buildViolation('Maximum ' . Adventurer::MAX_WEAPONS . ' armes autorisées.')
                    ->atPath('equipment')
                    ->addViolation();
            }
        }

        // Sac à dos : max 8 (potions, repas, objets de sac)
        if ($this->equipment->goesInBackpack()) {
            $backpackCount = 0;
            foreach ($this->adventurer->getAdventurerEquipments() as $ae) {
                if ($ae === $this) {
                    continue;
                }
                if ($ae->getEquipment()->goesInBackpack()) {
                    $backpackCount += $ae->getQuantity();
                }
            }
            if ($backpackCount + $this->quantity > Adventurer::MAX_BACKPACK) {
                $context->buildViolation('Sac à dos plein (max ' . Adventurer::MAX_BACKPACK . ' objets).')
                    ->atPath('quantity')
                    ->addViolation();
            }
        }

        // Slot unique : un seul objet par emplacement
        $slot = $this->equipment->getSlot();
        if ($slot !== null) {
            foreach ($this->adventurer->getAdventurerEquipments() as $ae) {
                if ($ae === $this) {
                    continue;
                }
                if ($ae->getEquipment()->getSlot() === $slot) {
                    $context->buildViolation('L\'emplacement « ' . $slot->label() . ' » est déjà occupé par ' . $ae->getEquipment()->getName() . '.')
                        ->atPath('equipment')
                        ->addViolation();
                    break;
                }
            }
        }
    }

    public function __toString(): string
    {
        $name = $this->equipment?->getName() ?? '?';

        return $this->quantity > 1 ? "{$name} (x{$this->quantity})" : $name;
    }
}
