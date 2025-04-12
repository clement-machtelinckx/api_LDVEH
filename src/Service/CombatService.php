<?php

namespace App\Service;

use App\Entity\Adventurer;
use App\Entity\Monster;

class CombatService
{
    public function fight(Adventurer $adventurer, Monster $monster): array
    {
        $adventurerScore = $adventurer->getAbility() + random_int(1, 10);
        $monsterScore = $monster->getAbility() + random_int(1, 10);

        $winner = $adventurerScore >= $monsterScore ? 'adventurer' : 'monster';

        return [
            'adventurer' => [
                'adventurerName' => $adventurer->getAdventurerName(),
                'base' => $adventurer->getAbility(),
                'roll' => $adventurerScore - $adventurer->getAbility(),
                'total' => $adventurerScore,
            ],
            'monster' => [
                'monsterName' => $monster->getMonsterName(),
                'base' => $monster->getAbility(),
                'roll' => $monsterScore - $monster->getAbility(),
                'total' => $monsterScore,
            ],
            'winner' => $winner,
            'log' => sprintf(
                'Adventurer: %d + %d = %d | Monster: %d + %d = %d → %s wins',
                $adventurer->getAbility(),
                $adventurerScore - $adventurer->getAbility(),
                $adventurerScore,
                $monster->getAbility(),
                $monsterScore - $monster->getAbility(),
                $monsterScore,
                ucfirst($winner)
            )
        ];
    }
}
