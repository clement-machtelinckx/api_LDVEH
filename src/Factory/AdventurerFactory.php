<?php

namespace App\Factory;

use App\Entity\Adventurer;
use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends PersistentProxyObjectFactory<Adventurer>
 */
final class AdventurerFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Adventurer::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'Ability' => self::faker()->numberBetween(10, 20),
            'AdventurerName' => self::faker()->firstName(),
            'Endurance' => self::faker()->numberBetween(20, 30),
            'user' => UserFactory::new(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public function named(string $name): static
    {
        return $this->with([
            'AdventurerName' => $name,
        ]);
    }

    public function forUser(UserFactory|User|Proxy $user): static
    {
        return $this->with([
            'user' => $user,
        ]);
    }

    public function withStats(int $ability, int $endurance): static
    {
        return $this->with([
            'Ability' => $ability,
            'Endurance' => $endurance,
        ]);
    }

    public function withHighStats(): static
    {
        return $this->with([
            'Ability' => 18,
            'Endurance' => 30,
        ]);
    }

    public function withLowStats(): static
    {
        return $this->with([
            'Ability' => 10,
            'Endurance' => 20,
        ]);
    }
}