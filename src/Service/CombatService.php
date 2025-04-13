<?php

namespace App\Service;

use App\Entity\Page;
use App\Entity\Monster;
use App\Entity\Adventurer;

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

    public function canAccessPage(Page $page, Adventurer $adventurer): bool
    {
        $monster = $page->getMonster();

        if (!$monster || !$page->isCombatIsBlocking()) {
            return true; // aucun monstre ou le combat est non-bloquant
        }

        return $this->hasDefeated($adventurer, $monster);
    }

    public function hasDefeated(Adventurer $adventurer, Monster $monster): bool
    {
        // TODO: implémenter cette logique correctement avec un historique ou un flag
        return true; // temporairement toujours vrai
    }

}
