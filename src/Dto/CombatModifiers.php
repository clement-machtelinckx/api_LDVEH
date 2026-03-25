<?php

namespace App\Dto;

/**
 * DTO transportant tous les bonus/malus de combat.
 * Le CombatService reçoit cet objet sans connaître Equipment/Skill.
 */
class CombatModifiers
{
    public function __construct(
        /** Bonus d'habileté total (Maîtrise des Armes +2, Puissance Psychique +2, sans arme -4) */
        public readonly int $abilityBonus = 0,

        /** L'ennemi est immunisé à la Puissance Psychique ? */
        public readonly bool $enemyImmuneToMindforce = false,

        /** L'aventurier possède le Bouclier Psychique ? */
        public readonly bool $hasPsychicShield = false,
    ) {}
}
