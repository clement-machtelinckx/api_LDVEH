<?php

namespace App\Factory;

use App\Entity\FightHistory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<FightHistory>
 */
final class FightHistoryFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return FightHistory::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'adventurer' => AdventurerFactory::new(),
            'monster' => MonsterFactory::new(),
            'victory' => self::faker()->boolean(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(FightHistory $fightHistory): void {})
        ;
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

    public function withoutMonster(): static
    {
        return $this->with([
            'monster' => null,
        ]);
    }
}
