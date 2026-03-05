<?php

namespace App\Factory;

use App\Entity\Adventurer;
use App\Entity\FightHistory;
use App\Entity\Monster;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends PersistentProxyObjectFactory<FightHistory>
 */
final class FightHistoryFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return FightHistory::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'adventurer' => AdventurerFactory::new(),
            'monster' => MonsterFactory::new(),
            'victory' => true,
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public function asVictory(): static
    {
        return $this->with([
            'victory' => true,
        ]);
    }

    public function asDefeat(): static
    {
        return $this->with([
            'victory' => false,
        ]);
    }

    public function forAdventurer(AdventurerFactory|Adventurer|Proxy $adventurer): static
    {
        return $this->with([
            'adventurer' => $adventurer,
        ]);
    }

    public function againstMonster(MonsterFactory|Monster|Proxy $monster): static
    {
        return $this->with([
            'monster' => $monster,
        ]);
    }

    public function withoutMonster(): static
    {
        return $this->with([
            'monster' => null,
        ]);
    }
}