<?php

namespace App\Factory;

use App\Entity\Equipment;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Equipment>
 */
final class EquipmentFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Equipment::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->randomElement([
                'Épée',
                'Sabre',
                'Lance',
                'Masse d’armes',
                'Potion de Laumspur',
                'Rations spéciales',
            ]),
            'description' => self::faker()->sentence(),
            'effect' => self::faker()->sentence(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public function weapon(string $name = 'Épée'): static
    {
        return $this->with([
            'name' => $name,
            'description' => 'Arme de combat.',
            'effect' => 'Peut être utilisée en combat.',
        ]);
    }

    public function meal(): static
    {
        return $this->with([
            'name' => 'Rations spéciales',
            'description' => 'Compte comme 1 repas.',
            'effect' => 'Repas',
        ]);
    }

    public function potion(): static
    {
        return $this->with([
            'name' => 'Potion de Laumspur',
            'description' => 'Potion de soin.',
            'effect' => '+4 ENDURANCE en fin de combat',
        ]);
    }
}