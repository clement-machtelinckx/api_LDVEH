<?php

namespace App\Service;

use App\Entity\Adventurer;
use App\Entity\Equipment;
use App\Enum\EquipmentType;
use Doctrine\ORM\EntityManagerInterface;

class EquipmentService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * Ajoute un équipement à l'aventurier en respectant les règles d'inventaire.
     * Retourne l'équipement retiré si remplacement de slot, null sinon.
     */
    public function addEquipment(Adventurer $adventurer, Equipment $equipment, int $quantity = 1): ?Equipment
    {
        $removed = null;

        // Slot occupé → remplacement automatique (ex: gilet → cotte de mailles)
        // Un équipement slotté ne peut exister qu'en un seul exemplaire
        if ($equipment->getSlot() !== null) {
            $quantity = 1;
            $removed = $this->findEquipmentInSlot($adventurer, $equipment);
            if ($removed) {
                $ae = $adventurer->findAdventurerEquipment($removed);
                if ($ae) {
                    $adventurer->removeEquipment($removed, $ae->getQuantity());
                }
            }
        }

        // Armes : max 2
        if ($equipment->getType() === EquipmentType::Weapon) {
            $weapons = $this->countByType($adventurer, EquipmentType::Weapon);
            if ($weapons >= Adventurer::MAX_WEAPONS) {
                throw new \LogicException('Impossible : maximum ' . Adventurer::MAX_WEAPONS . ' armes.');
            }
        }

        // Sac à dos : max 8 (somme des quantités)
        if ($equipment->goesInBackpack()) {
            $backpackCount = $this->countBackpack($adventurer);
            if ($backpackCount + $quantity > Adventurer::MAX_BACKPACK) {
                throw new \LogicException('Impossible : sac à dos plein (' . Adventurer::MAX_BACKPACK . ' objets max).');
            }
        }

        $adventurer->addEquipment($equipment, $quantity);
        $this->em->flush();

        return $removed;
    }

    /**
     * Remplace une arme par une nouvelle (quand on en a déjà 2).
     */
    public function replaceWeapon(Adventurer $adventurer, Equipment $oldWeapon, Equipment $newWeapon): void
    {
        if ($oldWeapon->getType() !== EquipmentType::Weapon || $newWeapon->getType() !== EquipmentType::Weapon) {
            throw new \LogicException('Les deux équipements doivent être des armes.');
        }

        $adventurer->removeEquipment($oldWeapon);
        $adventurer->addEquipment($newWeapon);
        $this->em->flush();
    }

    /**
     * Consomme un équipement (potion, repas) → retire 1 de l'inventaire.
     * Retourne le healAmount.
     */
    public function consume(Adventurer $adventurer, Equipment $equipment): int
    {
        if (!$equipment->isConsumable()) {
            throw new \LogicException($equipment->getName() . ' n\'est pas consommable.');
        }

        $healAmount = $equipment->getHealAmount();
        $adventurer->removeEquipment($equipment, 1);
        $this->em->flush();

        return $healAmount;
    }

    /**
     * Retire un équipement (donné à un PNJ, perdu, etc.)
     */
    public function removeEquipment(Adventurer $adventurer, Equipment $equipment, int $quantity = 1): void
    {
        $adventurer->removeEquipment($equipment, $quantity);
        $this->em->flush();
    }

    /**
     * Trouve l'équipement qui occupe le même slot que le nouvel item.
     */
    private function findEquipmentInSlot(Adventurer $adventurer, Equipment $newItem): ?Equipment
    {
        foreach ($adventurer->getAdventurerEquipments() as $ae) {
            $eq = $ae->getEquipment();
            if ($eq->getSlot() === $newItem->getSlot() && $eq->getSlot() !== null) {
                return $eq;
            }
        }

        return null;
    }

    private function countByType(Adventurer $adventurer, EquipmentType $type): int
    {
        $count = 0;
        foreach ($adventurer->getAdventurerEquipments() as $ae) {
            if ($ae->getEquipment()->getType() === $type) {
                $count += $ae->getQuantity();
            }
        }

        return $count;
    }

    private function countBackpack(Adventurer $adventurer): int
    {
        $count = 0;
        foreach ($adventurer->getAdventurerEquipments() as $ae) {
            if ($ae->getEquipment()->goesInBackpack()) {
                $count += $ae->getQuantity();
            }
        }

        return $count;
    }
}
