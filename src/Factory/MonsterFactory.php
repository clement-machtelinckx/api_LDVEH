<?php

namespace App\Factory;

use App\Entity\Monster;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Monster>
 */
final class MonsterFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Monster::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        $monsterNames = ['Goblin', 'Orc', 'Troll', 'Dragon', 'Skeleton', 'Zombie', 'Spider', 'Wolf'];
        
        return [
            'monsterName' => self::faker()->randomElement($monsterNames),
            'ability' => self::faker()->numberBetween(8, 18),
            'endurance' => self::faker()->numberBetween(10, 25),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Monster $monster): void {})
        ;
    }

    public function weak(): static
    {
        return $this->with([
            'ability' => self::faker()->numberBetween(5, 10),
            'endurance' => self::faker()->numberBetween(5, 10),
        ]);
    }

    public function strong(): static
    {
        return $this->with([
            'ability' => self::faker()->numberBetween(16, 22),
            'endurance' => self::faker()->numberBetween(20, 30),
        ]);
    }

    public function withStats(int $ability, int $endurance): static
    {
        return $this->with([
            'ability' => $ability,
            'endurance' => $endurance,
        ]);
    }
}
