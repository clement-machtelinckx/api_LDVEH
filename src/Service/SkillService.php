<?php

namespace App\Service;

use App\Dto\CombatModifiers;
use App\Entity\Adventurer;
use App\Entity\Skill;
use App\Enum\EquipmentType;
use Doctrine\ORM\EntityManagerInterface;

class SkillService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * Ajoute une discipline Kaï à l'aventurier (max 5).
     */
    public function addSkill(Adventurer $adventurer, Skill $skill): void
    {
        if ($adventurer->getSkills()->count() >= Adventurer::MAX_SKILLS) {
            throw new \LogicException('Impossible : maximum ' . Adventurer::MAX_SKILLS . ' disciplines Kaï.');
        }

        $slug = $skill->getSlug();
        if ($slug === null || $slug === '') {
            throw new \LogicException('La discipline Kaï fournie n\'a pas de slug valide.');
        }

        if ($adventurer->hasSlug($slug)) {
            throw new \LogicException('L\'aventurier possède déjà la discipline ' . $skill->getName() . '.');
        }

        $adventurer->addSkill($skill);
        $this->em->flush();
    }

    /**
     * Guérison : +1 END par paragraphe sans combat.
     * Appelé quand l'aventurier navigue vers un nouveau paragraphe sans combattre.
     * Retourne les points récupérés (0 ou 1).
     */
    public function applyHealing(Adventurer $adventurer): int
    {
        if (!$adventurer->hasSlug('guerison')) {
            return 0;
        }

        $effectiveMax = $adventurer->getEffectiveMaxEndurance();

        if ($adventurer->getEndurance() >= $effectiveMax) {
            return 0;
        }

        $adventurer->setEndurance($adventurer->getEndurance() + 1);
        $this->em->flush();

        return 1;
    }

    /**
     * Chasse : vérifie si l'aventurier est dispensé de manger.
     */
    public function isExemptFromMeal(Adventurer $adventurer): bool
    {
        return $adventurer->hasSlug('chasse');
    }

    public function handleMeal(Adventurer $adventurer, EquipmentService $equipmentService): bool
    {
        // Chasse dispense de manger
        if ($this->isExemptFromMeal($adventurer)) {
            return true;
        }

        // Cherche un repas dans l'inventaire
        $meal = $this->findEquipmentByType($adventurer, EquipmentType::Meal);

        if ($meal) {
            $equipmentService->consume($adventurer, $meal);
            return true;
        }

        // Pas de repas → -3 END
        $adventurer->setEndurance(max(0, $adventurer->getEndurance() - 3));
        $this->em->flush();

        return false;
    }

    /**
     * Calcule le bonus d'HABILETÉ au combat apporté par les disciplines.
     * - Maîtrise des Armes + arme maîtrisée équipée → +2
     * - Puissance Psychique (sauf ennemi immunisé) → +2
     */
    public function getCombatAbilityBonus(Adventurer $adventurer, bool $enemyImmuneToMindforce = false): int
    {
        $bonus = 0;

        // Maîtrise des Armes : +2 si combat avec l'arme maîtrisée
        if ($adventurer->hasSlug('maitrise_armes') && $adventurer->getMasteredWeaponSlug() !== null) {
            if ($this->hasMasteredWeaponEquipped($adventurer)) {
                $bonus += 2;
            }
        }

        // Puissance Psychique : +2 (sauf si ennemi immunisé)
        if ($adventurer->hasSlug('puissance_psychique') && !$enemyImmuneToMindforce) {
            $bonus += 2;
        }

        return $bonus;
    }

    /**
     * Malus si pas d'arme : -4 HABILETÉ.
     */
    public function getNoWeaponPenalty(Adventurer $adventurer): int
    {
        return $adventurer->hasWeapon() ? 0 : -4;
    }

    /**
     * Bouclier Psychique : protège contre les agressions mentales.
     * Retourne true si l'aventurier est protégé.
     */
    public function hasPsychicShield(Adventurer $adventurer): bool
    {
        return $adventurer->hasSlug('bouclier_psychique');
    }

    /**
     * Construit le DTO CombatModifiers avec tous les bonus/malus.
     * C'est ce DTO que le CombatService reçoit.
     */
    public function buildCombatModifiers(Adventurer $adventurer, bool $enemyImmuneToMindforce = false): CombatModifiers
    {
        $abilityBonus = $this->getCombatAbilityBonus($adventurer, $enemyImmuneToMindforce)
            + $this->getNoWeaponPenalty($adventurer);

        return new CombatModifiers(
            abilityBonus: $abilityBonus,
            enemyImmuneToMindforce: $enemyImmuneToMindforce,
            hasPsychicShield: $this->hasPsychicShield($adventurer),
        );
    }

    /**
     * Vérifie si l'aventurier a son arme maîtrisée équipée.
     */
    private function hasMasteredWeaponEquipped(Adventurer $adventurer): bool
    {
        $masteredSlug = $adventurer->getMasteredWeaponSlug();

        foreach ($adventurer->getAdventurerEquipments() as $ae) {
            $eq = $ae->getEquipment();
            if ($eq->getType() === EquipmentType::Weapon && $eq->getSlug() === $masteredSlug) {
                return true;
            }
        }

        return false;
    }

    /**
     * Trouve un équipement d'un type donné dans l'inventaire.
     */
    private function findEquipmentByType(Adventurer $adventurer, EquipmentType $type): ?\App\Entity\Equipment
    {
        foreach ($adventurer->getAdventurerEquipments() as $ae) {
            if ($ae->getEquipment()->getType() === $type) {
                return $ae->getEquipment();
            }
        }

        return null;
    }
}
