<?php

namespace App\Factory;

use App\Entity\Monster;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Monster>
 */
final class MonsterFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Monster::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'monsterName' => self::faker()->randomElement([
                'Giak',
                'Drakkar',
                'Vordak',
                'Monstre des glaces',
                'Kraan',
                'Gourgaz',
            ]),
            'ability' => self::faker()->numberBetween(10, 22),
            'endurance' => self::faker()->numberBetween(15, 35),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public function named(string $name): static
    {
        return $this->with([
            'monsterName' => $name,
        ]);
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