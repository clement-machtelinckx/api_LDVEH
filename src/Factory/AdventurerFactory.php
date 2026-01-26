<?php

namespace App\Factory;

use App\Entity\Adventurer;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Adventurer>
 */
final class AdventurerFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Adventurer::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'AdventurerName' => self::faker()->firstName(),
            'Ability' => self::faker()->numberBetween(10, 20),
            'Endurance' => self::faker()->numberBetween(15, 30),
            'user' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Adventurer $adventurer): void {})
        ;
    }

    public function withHighStats(): static
    {
        return $this->with([
            'Ability' => self::faker()->numberBetween(18, 24),
            'Endurance' => self::faker()->numberBetween(25, 35),
        ]);
    }

    public function withLowStats(): static
    {
        return $this->with([
            'Ability' => self::faker()->numberBetween(8, 12),
            'Endurance' => self::faker()->numberBetween(10, 15),
        ]);
    }

    public function withStats(int $ability, int $endurance): static
    {
        return $this->with([
            'Ability' => $ability,
            'Endurance' => $endurance,
        ]);
    }
}
